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

        //TEST: exclusive ClassroomSubjects
        // $problems = self::exclusiveClassroomSubjects($problems, $this->plan);

        //TEST: timeslots gaps

        //TEST: duplicates
        $problems = self::duplicates($problems, $this->plan);

        //TEST: profilic subjects in profilic classrooms only
        
        //TEST: profilic subjects NOT in other profilic classrooms

        //TEST: max 2 lessons with one subject by day

        //TEST: 2 lessons with one subject one by one

        //TEST: lessons by teachers not assigned to proper subject

        //TEST: proper amount of lessons by subject


        //set test values

        if(count($problems) > 0) {
            $problems = json_encode($problems, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $problems);
            $this->plan->test_details = $problems;
            $this->plan->test = 0;
        }
        else {
            $this->plan->test_details = null;
            $this->plan->test = 1;
        }
        $this->plan->save();

    }

    private static function exclusiveClassroomSubjects(array $problems, Plan $plan): array {
        $lessons = DB
            ::table('lessons')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'lessons.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->leftJoin('subjects as plan_subjects', 'plan_subjects.id', '=', 'lessons.subject_id')
            ->leftJoin('classroom_subjects', 'classrooms.id', '=', 'classroom_subjects.classroom_id')
            ->leftJoin('subjects as exclusive_subjects', 'exclusive_subjects.id', '=', 'classroom_subjects.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'exclusive_subjects.name as exclusive_subject', 'plan_subjects.name as plan_subject')
            ->where('lessons.plan_id', $plan->id)
            ->where('classroom_subjects.exclusive', 1)
            ->whereColumn('exclusive_subjects.id', "!=", 'plan_subjects.id')
            ->whereNotNull('exclusive_subjects.id')
            ->get();

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

        return $problems;
    }

    private static function duplicates(array $problems, Plan $plan): array {

        //more lessons in the same classroom
        $lessons = DB
            ::table('lessons as l')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'l.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'l.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'l.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'l.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'l.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'l.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, teachers.last_name) AS teacher"))
            ->where('plan_id', $plan->id)
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as x')
                    ->whereRaw('x.id <> l.id')
                    ->whereColumn('x.plan_id', 'l.plan_id')
                    ->whereColumn('x.weekday_id', 'l.weekday_id')
                    ->whereColumn('x.timeslot_id', 'l.timeslot_id')
                    ->whereColumn('x.classroom_id', 'l.classroom_id');
                });
            })
            ->orderBy("l.weekday_id")
            ->orderBy("l.timeslot_id")
            ->orderBy("l.classroom_id")
            ->get();

        // LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $lessons);

        foreach($lessons as $lesson) {
            $problem["description"] = "Więcej niż jedna lekcja w tej samej sali";
            $problem["weekday"] = $lesson->weekday;
            $problem["timeslot"] = $lesson->timeslot;
            $problem["classroom"] = $lesson->classroom;
            $problem["cohort"] = $lesson->cohort;
            $problem["subject"] = $lesson->subject;
            $problem["teacher"] = $lesson->teacher;

            $problems[] = $problem;
        }

        //more lessons of one cohort in the same timeslots
        $lessons = DB
            ::table('lessons as l')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'l.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'l.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'l.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'l.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'l.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'l.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, teachers.last_name) AS teacher"))
            ->where('plan_id', $plan->id)
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as x')
                    ->whereRaw('x.id <> l.id')
                    ->whereColumn('x.plan_id', 'l.plan_id')
                    ->whereColumn('x.weekday_id', 'l.weekday_id')
                    ->whereColumn('x.timeslot_id', 'l.timeslot_id')
                    ->whereColumn('x.cohort_id', 'l.cohort_id');
                });
            })
            ->orderBy("l.weekday_id")
            ->orderBy("l.timeslot_id")
            ->get();

        foreach($lessons as $lesson) {
            $problem["description"] = "Klasa ma więcej lekcji w tym samym czasie";
            $problem["weekday"] = $lesson->weekday;
            $problem["timeslot"] = $lesson->timeslot;
            $problem["classroom"] = $lesson->classroom;
            $problem["cohort"] = $lesson->cohort;
            $problem["subject"] = $lesson->subject;
            $problem["teacher"] = $lesson->teacher;

            $problems[] = $problem;
        }

        //teachers in more than one timeslots
        $lessons = DB
            ::table('lessons as l')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'l.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'l.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'l.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'l.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'l.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'l.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, teachers.last_name) AS teacher"))
            ->where('plan_id', $plan->id)
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as x')
                    ->whereRaw('x.id <> l.id')
                    ->whereColumn('x.plan_id', 'l.plan_id')
                    ->whereColumn('x.weekday_id', 'l.weekday_id')
                    ->whereColumn('x.timeslot_id', 'l.timeslot_id')
                    ->whereColumn('x.teacher_id', 'l.teacher_id');
                });
            })
            ->orderBy("l.weekday_id")
            ->orderBy("l.timeslot_id")
            ->get();

        foreach($lessons as $lesson) {
            $problem["description"] = "Nauczyciel ma więcej lekcji w tym samym czasie";
            $problem["weekday"] = $lesson->weekday;
            $problem["timeslot"] = $lesson->timeslot;
            $problem["classroom"] = $lesson->classroom;
            $problem["cohort"] = $lesson->cohort;
            $problem["subject"] = $lesson->subject;
            $problem["teacher"] = $lesson->teacher;

            $problems[] = $problem;
        }

        return $problems;
    }
}
