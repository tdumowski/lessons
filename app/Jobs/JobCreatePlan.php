<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Plan;
use App\Models\Season;
use App\Models\User;
use Illuminate\Support\Carbon;

class JobCreatePlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $plan = new Plan();
        $plan->school_id = $this->user->school->id;
        $plan->season_id = Season::where("status", "ACTIVE")->first()->id;
        $plan->name = Carbon::now()->timezone('Europe/Warsaw')->format('Y-m-d H:i');;
        $plan->created_by = $this->user->id;
        $plan->save();
    }
}
