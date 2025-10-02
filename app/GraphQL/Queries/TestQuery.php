<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Jobs\JobVerifyPlan;
use App\Jobs\JobSendMail;
use App\Models\Plan;

final readonly class TestQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): bool
    {
        // $plan = Plan::find(38);

        // JobVerifyPlan::dispatch(
        //     plan: $plan
        // );
        
        JobSendMail::dispatch(
            // plan: $plan
        );

        return true;
    }
}
