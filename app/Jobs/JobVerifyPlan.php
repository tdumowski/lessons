<?php

namespace App\Jobs;

use App\Models\Lesson;
use App\Models\LogRepository;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class JobVerifyPlan implements ShouldQueue
{
    use Queueable;

    public Plan $plan;

    /**
     * Create a new job instance.
     */
    public function __construct(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $problems = [];

        //TEST: Exclusive ClassroomSubjects
        $lessons = DB
            ::table('lessons')
            ->leftJoin('weekdays', 'lessons.weekday_id', '=', 'weekdays.id')
            ->leftJoin('timeslots', 'lessons.timeslot_id', '=', 'timeslots.id')
            ->leftJoin('classrooms', 'lessons.classroom_id', '=', 'classrooms.id')
            ->leftJoin('cohorts', 'lessons.cohort_id', '=', 'cohorts.id')
            ->leftJoin('subjects as plan_subjects', 'lessons.subject_id', '=', 'plan_subjects.id')
            ->leftJoin('classroom_subjects', 'classroom_subjects.classroom_id', '=', 'classrooms.id')
            ->leftJoin('subjects as exclusive_subjects', 'classroom_subjects.subject_id', '=', 'exclusive_subjects.id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'exclusive_subjects.name as exclusive_subject', 'plan_subjects.name as plan_subject')
            ->where('lessons.plan_id', $this->plan->id)
            ->where('classroom_subjects.exclusive', 1)
            ->whereColumn('exclusive_subjects.id', "!=", 'plan_subjects.id')
            ->whereNotNull('exclusive_subjects.id')
            ->get();

        LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n count: " . $lessons->count());

        foreach($lessons as $lesson) {
            $problem["description"] = "Niedozwolony przedmiot w sali exclusive";
            $problem["weekday"] = $lesson->weekday;
            $problem["timeslot"] = $lesson->timeslot;
            $problem["cohort"] = $lesson->cohort;
            $problem["classroom"] = $lesson->classroom;
            $problem["exclusive_subject"] = $lesson->exclusive_subject;
            $problem["plan_subject"] = $lesson->plan_subject;

            $problems[] = $problem;
        }


        
        //set test values

        if(count($problems) > 0) {
            $problems = json_encode($problems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $problems);
            $this->plan->test_comment = $problems;
            $this->plan->test = 0;
        }
        else {
            $this->plan->test_comment = null;
            $this->plan->test = 1;
        }
        $this->plan->save();
    }
}
