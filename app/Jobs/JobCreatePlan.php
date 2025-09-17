<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Plan;

class JobCreatePlan implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $plan = new Plan();
        $plan->school_id = 1;
        $plan->season_id = 1;
        $plan->name = "test_name";
        $plan->created_by = $this->user->id;
        $plan->save();
    }
}
