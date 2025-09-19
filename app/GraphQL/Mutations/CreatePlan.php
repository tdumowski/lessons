<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Jobs\JobCreatePlan;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

final readonly class CreatePlan
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // $user = Auth::user();
        $user = User::find(1);

        if(JobCreatePlan::dispatch($user)) {
            return true;
        }

        return false;
    }
}
