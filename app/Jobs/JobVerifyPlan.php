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
        $problemsCritical = [];
        $problemsSoft = [];

        //TEST: exclusive ClassroomSubjects
        // $problemsCritical = self::exclusiveClassroomSubjects($problemsCritical, $this->plan);

        //TEST: timeslots gaps
        // $problemsSoft = self::timeslotsGaps($problemsSoft, $this->plan);

        //TEST: duplicates
        // $problemsCritical = self::duplicates($problemsCritical, $this->plan);

        //TEST: profile subjects in their classrooms only
        // $problemsCritical = self::profileSubjectsClassrooms($problemsCritical, $this->plan);
        
        //TEST: max 2 lessons with one subject by day

        //TEST: 2 lessons with one subject one by one

        //TEST: lessons by teachers not assigned to proper subject

        //TEST: proper amount of lessons by subject


        if(count($problemsCritical) > 0) {
            $problemsCritical = json_encode($problemsCritical, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $problemsCritical);
            $this->plan->test_critical = 0;
            $this->plan->test_critical_details = $problemsCritical;
        }
        else {
            $this->plan->test_critical = 1;
            $this->plan->test_critical_details = null;
        }

        if(count($problemsSoft) > 0) {
            $problemsSoft = json_encode($problemsSoft, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $problemsSoft);
            $this->plan->test_soft = 0;
            $this->plan->test_soft_details = $problemsSoft;
        }
        else {
            $this->plan->test_soft = 1;
            $this->plan->test_soft_details = null;
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
            $problem["Błąd"] = "Niedozwolony przedmiot w sali exclusive";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->timeslot;
            $problem["Klasa"] = $lesson->cohort;
            $problem["Sala"] = $lesson->classroom;
            $problem["Przedmiot dozwolony"] = $lesson->exclusive_subject;
            $problem["Przedmiot w planie"] = $lesson->plan_subject;

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
            $problem["Błąd"] = "Więcej niż jedna lekcja w tej samej sali";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->timeslot;
            $problem["Sala"] = $lesson->classroom;
            $problem["Klasa"] = $lesson->cohort;
            $problem["Przedmiot"] = $lesson->subject;
            $problem["Nauczyciel"] = $lesson->teacher;

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
            $problem["Błąd"] = "Klasa ma więcej lekcji w tym samym czasie";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->timeslot;
            $problem["Sala"] = $lesson->classroom;
            $problem["Klasa"] = $lesson->cohort;
            $problem["Przedmiot"] = $lesson->subject;
            $problem["Nauczyciel"] = $lesson->teacher;

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
            $problem["Błąd"] = "Nauczyciel ma więcej lekcji w tym samym czasie";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->timeslot;
            $problem["Sala"] = $lesson->classroom;
            $problem["Klasa"] = $lesson->cohort;
            $problem["Przedmiot"] = $lesson->subject;
            $problem["Nauczyciel"] = $lesson->teacher;

            $problems[] = $problem;
        }

        return $problems;
    }

    private static function timeslotsGaps($problems, Plan $plan): array {
        $lessons = DB::table('lessons as l1')
            ->join('timeslots as ts1', 'ts1.id', '=', 'l1.timeslot_id')
            ->join('weekdays', 'weekdays.id', '=', 'l1.weekday_id')
            ->join('cohorts', 'cohorts.id', '=', 'l1.cohort_id')
            ->join('lessons as l2', function($join) {
                $join->on('l2.plan_id', '=', 'l1.plan_id')
                    ->on('l2.weekday_id', '=', 'l1.weekday_id')
                    ->on('l2.cohort_id', '=', 'l1.cohort_id');
            })
            ->join('timeslots as ts2', 'ts2.id', '=', 'l2.timeslot_id')
            ->join('timeslots as ts_missing', function($join) {
                $join->on('ts_missing.order', '>', 'ts1.order')
                    ->on('ts_missing.order', '<', 'ts2.order');
            })
            ->where('l1.plan_id', $plan->id)
            ->whereColumn('ts2.order', '>', 'ts1.order')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('lessons as lx')
                    ->join('timeslots as tx', 'tx.id', '=', 'lx.timeslot_id')
                    ->whereColumn('lx.plan_id', 'l1.plan_id')
                    ->whereColumn('lx.weekday_id', 'l1.weekday_id')
                    ->whereColumn('lx.cohort_id', 'l1.cohort_id')
                    ->whereColumn('tx.order', 'ts_missing.order');
            })
            ->select('weekdays.name as weekday', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"), 'ts_missing.start as missing_timeslot_start')
            ->groupBy('weekdays.name', 'cohort', 'ts_missing.id', 'ts_missing.start', 'ts_missing.order')
            ->orderBy('l1.weekday_id')
            ->orderBy('l1.cohort_id')
            ->orderBy('ts_missing.order')
            ->get();

        foreach($lessons as $lesson) {
            $problem["Błąd"] = "Okienka pomiędzy lekcjami";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->missing_timeslot_start;
            $problem["Klasa"] = $lesson->cohort;

            $problems[] = $problem;
        }

        return $problems;
    }

    private static function profileSubjectsClassrooms($problems, Plan $plan): array {
        $lessons = DB::table('lessons as l')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'l.weekday_id')
            ->join('timeslots', 'timeslots.id', '=', 'l.timeslot_id')
            ->join('classrooms', 'classrooms.id', '=', 'l.classroom_id')
            ->leftJoin('classroom_subjects', 'classroom_subjects.subject_id', '=', 'l.subject_id')
            ->leftJoin('classrooms as classrooms2', 'classrooms2.id', '=', 'classroom_subjects.classroom_id')
            ->join('cohorts', 'cohorts.id', '=', 'l.cohort_id')
            ->join('teachers', 'teachers.id', '=', 'l.teacher_id')
            ->join('subjects', 'subjects.id', '=', 'l.subject_id')
            ->where('l.plan_id', $plan->id)
            ->whereColumn('subjects.id', 'classroom_subjects.subject_id')
            ->whereColumn('classrooms.id', "!=", 'classrooms2.id')
            ->where(function ($query) {
                $query->whereColumn('classrooms.id', 'classrooms2.id')
                    ->orWhereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('classroom_subjects as cs2')
                            ->whereColumn('cs2.classroom_id', 'l.classroom_id')
                            ->whereColumn('cs2.subject_id', 'subjects.id');
                    });
            })
            ->select(
                'weekdays.name as weekday',
                'timeslots.start as timeslot',
                'classrooms.name as classroom',
                DB::raw("CONCAT(cohorts.level, cohorts.line) as cohort"),
                DB::raw("CONCAT(teachers.first_name, teachers.last_name) as teacher"),
                'subjects.name as subject',
                'classrooms.name as plan_classroom',
                'classrooms2.name as required_classroom'
            )
            ->get();

        // LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n count: " . $lessons->count());

        foreach($lessons as $lesson) {
            $problem["Błąd"] = "Lekcje profilowe w nie swoich salach";
            $problem["Dzień"] = $lesson->weekday;
            $problem["Godzina"] = $lesson->timeslot;
            $problem["Klasa"] = $lesson->cohort;
            $problem["Przedmiot"] = $lesson->subject;
            $problem["Sala wymagana"] = $lesson->required_classroom;
            $problem["Sala w planie"] = $lesson->plan_classroom;

            $problems[] = $problem;
        }

        return $problems;
    }
}
