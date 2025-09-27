<?php

namespace App\Jobs;

use App\Models\ChatRepository;
use App\Models\CohortSubject;
use App\Models\LogRepository;
use App\Models\PromptRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class JobCreatePlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Collection $cohorts;
    public string $initialPrompt;
    public string $generalDatasetsPrompt;
    public string $rulesPrompt;
    public int $cohortIndex;
    public string $newPlan;
    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $cohorts, string $initialPrompt, string $generalDatasetsPrompt, string $rulesPrompt, int $cohortIndex, 
        string $newPlan, User $user)
    {
        $this->user = $user;
        $this->cohorts = $cohorts;
        $this->initialPrompt = $initialPrompt;
        $this->generalDatasetsPrompt = $generalDatasetsPrompt;
        $this->rulesPrompt = $rulesPrompt;
        $this->cohortIndex = $cohortIndex;
        $this->newPlan = $newPlan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        //get n-cohort
        $cohort = ($this->cohorts->has($this->cohortIndex)) ? $this->cohorts[$this->cohortIndex] : null;
        
        if($cohort) {
            LogRepository::saveLogFile("log", "INFO (Job JobCreatePlan): cohort[{$this->cohortIndex}] found, processing new prompt");

            //cohort with given index exists -> the prompt is generated and sent to LLM
            $cohortSubjects = CohortSubject::where("status", "ACTIVE")->where("cohort_id", $cohort->id)->get();

            //get subjects assigned to teh current cohort
            $cohortSubjectsPrompt = "klasy_przedmioty: " . $cohortSubjects->map(function ($cohortSubject) {
                return [
                    'klasa_id' => $cohortSubject->cohort_id,
                    'klasa_nazwa' => $cohortSubject->cohort_level . $cohortSubject->cohort_line,
                    'przedmiot_id' => $cohortSubject->subject_id,
                    'przedmiot_nazwa' => $cohortSubject->subject->name,
                    'liczba_lekcji_tygodniowo' => $cohortSubject->amount,
                    'nauczyciel_id' => $cohortSubject->teacher_id,
                    'nauczyciel_nazwisko' => $cohortSubject->teacher->first_name . " " . $cohortSubject->teacher->last_name,
                ];
            })->toJson(JSON_UNESCAPED_UNICODE) . "; ";

            //scope of cohorts
            $cohortScopePrompt = PromptRepository::getPrompt_4_Scope($cohort);

            //scope of cohorts
            $collisionsPrompt = ($this->newPlan == "") ? "" : PromptRepository::getPrompt_5_Colisions($this->newPlan);

            //concatenate partial sub-prompts into one prompt
            $prompt = $this->initialPrompt . " " . $this->generalDatasetsPrompt . " " . $cohortSubjectsPrompt . " " . $this->rulesPrompt. " " . 
                $cohortScopePrompt . " " . $collisionsPrompt;

            //save prompt in the log file
            LogRepository::saveLogFile("chat", "PROMPT:\n".$prompt);

            //send prompt to LLM
            $chatAnswer = ChatRepository::getChatAnswer($prompt);
            $chatAnswer = self::extractJsonBlock($chatAnswer);

            //save answer in the log file
            LogRepository::saveLogFile("chat", "ANSWER:\n".$chatAnswer);

            //merge newPlan with the latest chat's answer
            if($chatAnswer) {
                $this->newPlan = ($this->newPlan == "") ? $chatAnswer : self::mergeJsons($this->newPlan, $chatAnswer);
            }

            $this->cohortIndex += 1;

            LogRepository::saveLogFile("log", "INFO (Job JobCreatePlan): next job triggered, cohortIndex: {$this->cohortIndex}");

            JobCreatePlan::dispatch(
                user: $this->user,
                cohorts: $this->cohorts,
                initialPrompt: $this->initialPrompt,
                generalDatasetsPrompt: $this->generalDatasetsPrompt,
                rulesPrompt: $this->rulesPrompt,
                cohortIndex: $this->cohortIndex,
                newPlan: $this->newPlan
            );
        }
        else {
            //cohort with given index NOT exists -> the newPlan json id saved in database and email is sent
            LogRepository::saveLogFile("log", "INFO (Job JobCreatePlan): cohort[{$this->cohortIndex}] NOT found, saving the data into DB");

            $this->newPlan = self::cleanJson($this->newPlan);

            JobSavePlan::dispatch(
                user: $this->user,
                newPlan: $this->newPlan
            );


        }
    }

    private static function cleanJson(string $string) {
        return json_encode(json_decode($string, true), JSON_UNESCAPED_UNICODE);;
    }

    private static function extractJsonBlock(string $string) {
        $start = strpos($string, '[');
        $end = strrpos($string, ']');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonCandidate = substr($string, $start, $end - $start + 1);
            if(json_validate($jsonCandidate)) {
                return $jsonCandidate;
            }
        }
        return null;
    }

    private static function mergeJsons(string $newPlan, string $chatAnswer): string {
        $array1 = json_decode($newPlan, true);
        $array2 = json_decode($chatAnswer, true);

        $mergedArray = array_merge($array1, $array2);

        $mergedJson = json_encode($mergedArray, JSON_PRETTY_PRINT);

        return $mergedJson;
    }
}
