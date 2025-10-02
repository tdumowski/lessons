<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Jobs\JobCreatePlan;
use App\Models\PromptRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Cohort;
use App\Models\LogRepository;
use App\Models\Season;
use App\Models\User;


final readonly class CreatePlan
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // $user = Auth::user();
        $user = User::find(1);

        //get cohorts from user's school
        $season = Season::where("status", "ACTIVE")->first();
        if(!$season) {
            LogRepository::saveLogFile("log", "ERROR (Mutation CreatePlan): no active season");
            return;
        }

        $cohortIds = [1,2,3,4];
        // $cohortIds = [1];

        // $cohorts = Cohort::where("status", "ACTIVE")->where("season_id", $season->id)->get();
        $cohorts = Cohort::where("status", "ACTIVE")->where("season_id", $season->id)->whereIn("id", $cohortIds)->get();
        if(!$cohorts) {
            LogRepository::saveLogFile("log", "ERROR (Mutation CreatePlan): no active cohort");
            return;
        }

        $initialPrompt = PromptRepository::getPrompt_1_Initial();
        LogRepository::saveLogFile("chat", "INITIAL PROMPT:\n".$initialPrompt);

        $generalDatasetsPrompt = PromptRepository::getPrompt_2_GeneralDatasets();
        LogRepository::saveLogFile("chat", "DATASETS PROMPT:\n".$generalDatasetsPrompt);

        $rulesPrompt = PromptRepository::getPrompt_3_Rules();
        LogRepository::saveLogFile("chat", "RULES PROMPT:\n".$rulesPrompt);

        LogRepository::saveLogFile("log", "INFO (Mutation CreatePlan): first job triggered");

        if(JobCreatePlan::dispatch(
            cohorts: $cohorts,
            initialPrompt: $initialPrompt,
            generalDatasetsPrompt: $generalDatasetsPrompt,
            rulesPrompt: $rulesPrompt,
            cohortIndex: 0,
            newPlan: "",
            user: $user
            )
        ) 
        {
            return true;
        }

        return false;
    }
}
