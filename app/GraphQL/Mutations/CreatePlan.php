<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Jobs\JobCreatePlan;
use Illuminate\Support\Facades\Auth;

final readonly class CreatePlan
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $user = Auth::user();
        JobCreatePlan::dispatch($user);
    }
}
