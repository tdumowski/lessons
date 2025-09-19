<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Plan;
use App\Models\User;
use App\Models\Season;
use Illuminate\Database\Eloquent\Collection;

final readonly class GetPlans
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args): Collection
    {
        // $user = Auth::user();
        $user = User::find(1);

        $plans = Plan
            ::where("school_id", $user->school->id)
            ->where("status", "ACTIVE")
            ->where("season_id", Season::where("status", "ACTIVE")->first()->id)
            ->orderBy("created_at", "DESC")
            ->get();
        
        return $plans;
    }
}
