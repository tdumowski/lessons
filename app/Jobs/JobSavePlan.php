<?php

namespace App\Jobs;

use App\Models\Lesson;
use App\Models\LogRepository;
use App\Models\Plan;
use App\Models\School;
use App\Models\Season;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class JobSavePlan implements ShouldQueue
{
    use Queueable;

    public User $user;
    public School $school;
    public string $newPlan;

    /**
     * Create a new job instance.
     */
    public function __construct(string $newPlan, User $user)
    {
        $this->user = $user;
        $this->school = $this->user->school;
        $this->newPlan = $newPlan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        LogRepository::saveLogFile("log", "INFO (Job JobSavePlan): plan saving started");

        $plan = new Plan();
        $plan->school_id = $this->school->id;
        $plan->season_id = Season::where("status", "ACTIVE")->first()->id;
        $plan->name = Carbon::now()->timezone('Europe/Warsaw')->format('Y-m-d H:i');
        $plan->source = $this->newPlan;
        $plan->created_by = $this->user->id;
        
        if($plan->save()) {
            LogRepository::saveLogFile("log", "INFO (Job JobSavePlan): plan id {$plan->id} saved into DB, lessons saving started");
            LogRepository::saveLogFile("chat", "MERGED PLAN:\n".$this->newPlan);

            //saving data info DB
            $data = json_decode($this->newPlan, true); // tablica tablic asocjacyjnych

            $map = [
                'dzien_id'      => 'weekday_id',
                'slot_id'       => 'timeslot_id',
                'sala_id'       => 'classroom_id',
                'klasa_id'      => 'cohort_id',
                'przedmiot_id'  => 'subject_id',
                'nauczyciel_id' => 'teacher_id'
            ];

            $result = [];
            foreach ($data as $object) {
                $new = [];
                foreach ($object as $key => $value) {
                    $new[$map[$key] ?? $key] = $value;
                }
                $result[] = $new;
            }

            foreach ($result as $index => $object) {
                $lesson = new Lesson();
                $lesson->plan_id = $plan->id;
                $lesson->created_by = $this->user->id;

                foreach ($object as $key => $value) {
                    $lesson->$key = $value;
                }

                $lesson->save();
            }

            LogRepository::saveLogFile("log", "INFO (Job JobSavePlan): lessons saved into DB");
        }

        //trigger job to verify the data
        JobVerifyPlan::dispatch(
            plan: $plan
        );
    }
}
