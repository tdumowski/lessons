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
        $problemsCritical = self::exclusiveClassroomSubjects($problemsCritical, $this->plan);

        //TEST: timeslots gaps
        $problemsSoft = self::timeslotsGaps($problemsSoft, $this->plan);

        //TEST: duplicates (more than one cohort/classroom/teacher in the same timeslot)
        $problemsCritical = self::duplicates($problemsCritical, $this->plan);

        //TEST: profile subjects in their classrooms only
        $problemsCritical = self::profileSubjectsClassrooms($problemsCritical, $this->plan);
        
        //TEST: max 2 lessons with one subject by day
        $problemsSoft = self::dailyLessonsExceeded($problemsSoft, $this->plan);

        //TEST: 2 lessons with one subject one by one
        $problemsSoft = self::sameLessonsGaps($problemsSoft, $this->plan);

        //TEST: lessons by teachers not assigned to proper subject and class
        $problemsCritical = self::properSubjectTeachers($problemsCritical, $this->plan);

        //TEST: proper amount of lessons by subject
        $problemsCritical = self::amountOfSubjectLessons($problemsCritical, $this->plan);


        if(count($problemsCritical) > 0) {
            $problemsCritical = json_encode($problemsCritical, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            LogRepository::saveLogFile("log", "INFO (Job JobVerifyPlan): \n " . $problemsCritical);
            $this->plan->test_critical = 0;
            $this->plan->test_critical_details = $problemsCritical;
        }
        else {
            $this->plan->test_critical = 1;
            $this->plan->test_critical_details = null;
        }

        if(count($problemsSoft) > 0) {
            $problemsSoft = json_encode($problemsSoft, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->where('classroom_subjects.exclusive', 1)
            ->whereColumn('exclusive_subjects.id', "!=", 'plan_subjects.id')
            ->whereNotNull('exclusive_subjects.id')
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Niedozwolony przedmiot w sali exclusive";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["classroom"] = $lesson->classroom;
            $problemDetail["exclusive_subject"] = $lesson->exclusive_subject; //przedmiot przypisany do sali
            $problemDetail["plan_subject"] = $lesson->plan_subject; //przedmiot w planie

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function duplicates(array $problems, Plan $plan): array {

        //more lessons in the same classroom
        $lessons = DB
            ::table('lessons')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'lessons.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'lessons.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, ' ', teachers.last_name) AS teacher"))
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as lessons2')
                    ->whereRaw('lessons2.id <> lessons.id')
                    ->whereColumn('lessons2.plan_id', 'lessons.plan_id')
                    ->whereColumn('lessons2.weekday_id', 'lessons.weekday_id')
                    ->whereColumn('lessons2.timeslot_id', 'lessons.timeslot_id')
                    ->whereColumn('lessons2.classroom_id', 'lessons.classroom_id');
                });
            })
            ->orderBy("lessons.weekday_id")
            ->orderBy("lessons.timeslot_id")
            ->orderBy("lessons.classroom_id")
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Więcej niż jedna lekcja w tej samej sali";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["classroom"] = $lesson->classroom;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["teacher"] = $lesson->teacher;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        //more lessons of one cohort in the same timeslots
        $lessons = DB
            ::table('lessons')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'lessons.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'lessons.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, ' ', teachers.last_name) AS teacher"))
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as lessons2')
                    ->whereRaw('lessons2.id <> lessons.id')
                    ->whereColumn('lessons2.plan_id', 'lessons.plan_id')
                    ->whereColumn('lessons2.weekday_id', 'lessons.weekday_id')
                    ->whereColumn('lessons2.timeslot_id', 'lessons.timeslot_id')
                    ->whereColumn('lessons2.cohort_id', 'lessons.cohort_id');
                });
            })
            ->orderBy("lessons.weekday_id")
            ->orderBy("lessons.timeslot_id")
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Klasa ma więcej lekcji w tym samym czasie";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["classroom"] = $lesson->classroom;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["teacher"] = $lesson->teacher;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        //teachers in more than one timeslots
        $lessons = DB
            ::table('lessons')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->leftJoin('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->leftJoin('classrooms', 'classrooms.id', '=', 'lessons.classroom_id')
            ->leftJoin('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'lessons.teacher_id')
            ->leftJoin('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->select('weekdays.name as weekday', 'timeslots.start as timeslot', 'classrooms.name as classroom', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"),
                'subjects.name as subject', DB::raw("CONCAT(teachers.first_name, ' ', teachers.last_name) AS teacher"))
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->where(function ($query) {
                // duplikat w plan_id + weekday_id
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('lessons as lessons2')
                    ->whereRaw('lessons2.id <> lessons.id')
                    ->whereColumn('lessons2.plan_id', 'lessons.plan_id')
                    ->whereColumn('lessons2.weekday_id', 'lessons.weekday_id')
                    ->whereColumn('lessons2.timeslot_id', 'lessons.timeslot_id')
                    ->whereColumn('lessons2.teacher_id', 'lessons.teacher_id');
                });
            })
            ->orderBy("lessons.weekday_id")
            ->orderBy("lessons.timeslot_id")
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Nauczyciel ma więcej lekcji w tym samym czasie";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["classroom"] = $lesson->classroom;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["teacher"] = $lesson->teacher;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function timeslotsGaps($problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->join('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->join('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->join('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->join('lessons as lessons2', function($join) {
                $join->on('lessons2.plan_id', '=', 'lessons.plan_id')
                    ->on('lessons2.weekday_id', '=', 'lessons.weekday_id')
                    ->on('lessons2.cohort_id', '=', 'lessons.cohort_id');
            })
            ->join('timeslots as timeslots2', 'timeslots2.id', '=', 'lessons2.timeslot_id')
            ->join('timeslots as ts_missing', function($join) {
                $join->on('ts_missing.order', '>', 'timeslots.order')
                    ->on('ts_missing.order', '<', 'timeslots2.order');
            })
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->whereColumn('timeslots2.order', '>', 'timeslots.order')
            ->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('lessons as lessons3')
                    ->join('timeslots as timeslots3', 'timeslots3.id', '=', 'lessons3.timeslot_id')
                    ->whereColumn('lessons3.plan_id', 'lessons.plan_id')
                    ->whereColumn('lessons3.weekday_id', 'lessons.weekday_id')
                    ->whereColumn('lessons3.cohort_id', 'lessons.cohort_id')
                    ->whereColumn('timeslots3.order', 'ts_missing.order');
            })
            ->select('weekdays.name as weekday', DB::raw("CONCAT(cohorts.level,cohorts.line) AS cohort"), 'ts_missing.start as missing_timeslot')
            ->groupBy('weekdays.name', 'cohort', 'ts_missing.id', 'ts_missing.start', 'ts_missing.order')
            ->orderBy('lessons.weekday_id')
            ->orderBy('lessons.cohort_id')
            ->orderBy('ts_missing.order')
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Okienka pomiędzy lekcjami";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->missing_timeslot;
            $problemDetail["cohort"] = $lesson->cohort;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function profileSubjectsClassrooms($problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->leftJoin('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->join('timeslots', 'timeslots.id', '=', 'lessons.timeslot_id')
            ->join('classrooms', 'classrooms.id', '=', 'lessons.classroom_id')
            ->leftJoin('classroom_subjects', 'classroom_subjects.subject_id', '=', 'lessons.subject_id')
            ->leftJoin('classrooms as classrooms2', 'classrooms2.id', '=', 'classroom_subjects.classroom_id')
            ->join('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->join('teachers', 'teachers.id', '=', 'lessons.teacher_id')
            ->join('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->whereColumn('subjects.id', 'classroom_subjects.subject_id')
            ->whereColumn('classrooms.id', "!=", 'classrooms2.id')
            ->where(function ($query) {
                $query->whereColumn('classrooms.id', 'classrooms2.id')
                    ->orWhereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('classroom_subjects as classroom_subjects2')
                            ->whereColumn('classroom_subjects2.classroom_id', 'lessons.classroom_id')
                            ->whereColumn('classroom_subjects2.subject_id', 'subjects.id');
                    });
            })
            ->select(
                'weekdays.name as weekday',
                'timeslots.start as timeslot',
                'classrooms.name as classroom',
                DB::raw("CONCAT(cohorts.level, cohorts.line) as cohort"),
                DB::raw("CONCAT(teachers.first_name, ' ', teachers.last_name) as teacher"),
                'subjects.name as subject',
                'classrooms.name as plan_classroom',
                'classrooms2.name as required_classroom'
            )
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Lekcje profilowe w nie swoich salach";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["required_classroom"] = $lesson->required_classroom;
            $problemDetail["plan_classroom"] = $lesson->plan_classroom;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function dailyLessonsExceeded(array $problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->join('weekdays', 'weekdays.id', '=', 'lessons.weekday_id')
            ->join('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->join('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->groupBy("weekdays.name", "cohort", "subjects.name")
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->having('counter', ">", 2)
            ->select(
                'weekdays.name as weekday',
                DB::raw("CONCAT(cohorts.level, cohorts.line) as cohort"),
                'subjects.name as subject',
                DB::raw("COUNT(subject_id) as counter")
            )
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Przekroczona liczba lekcji jednego przedmiotu w ciągu dnia";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["counter"] = $lesson->counter;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function sameLessonsGaps(array $problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->join('weekdays', 'weekdays.id', '=','lessons.weekday_id')
            ->join('timeslots', 'timeslots.id', '=','lessons.timeslot_id')
            ->join('cohorts', 'cohorts.id', '=','lessons.cohort_id')
            ->join('subjects', 'subjects.id', '=','lessons.subject_id')
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->select(
                'lessons.weekday_id', 
                'weekdays.name as weekday', 
                'lessons.cohort_id', 
                DB::raw("CONCAT(cohorts.level, cohorts.line) as cohort"),
                'lessons.subject_id', 
                'subjects.name as subject',
                'lessons.id as lesson_id', 
                'lessons.timeslot_id', 
                'timeslots.start as timeslot', 
                'timeslots.order as timeslot_order')
            ->get()
            ->groupBy(function ($item) {
                return $item->weekday_id . '-' . $item->cohort_id . '-' . $item->subject_id;
            })
            ->flatMap(function ($group) {
                $sorted = collect($group)->sortBy('timeslot_order')->values();
                $results = [];

                for ($i = 0; $i < $sorted->count(); $i++) {
                    for ($j = $i + 1; $j < $sorted->count(); $j++) {
                        $diff = abs($sorted[$i]->timeslot_order - $sorted[$j]->timeslot_order);
                        if ($diff > 1) {
                            $results[] = [
                                'weekday_id'     => $sorted[$i]->weekday_id,
                                'weekday'        => $sorted[$i]->weekday,
                                'cohort_id'      => $sorted[$i]->cohort_id,
                                'cohort'         => $sorted[$i]->cohort,
                                'subject_id'     => $sorted[$i]->subject_id,
                                'subject'        => $sorted[$i]->subject,
                                'lesson_id'      => $sorted[$i]->lesson_id,
                                'timeslot_id'    => $sorted[$i]->timeslot_id,
                                'timeslot'       => $sorted[$i]->timeslot,
                            ];
                            $results[] = [
                                'weekday_id'     => $sorted[$j]->weekday_id,
                                'weekday'        => $sorted[$i]->weekday,
                                'cohort_id'      => $sorted[$j]->cohort_id,
                                'cohort'         => $sorted[$i]->cohort,
                                'subject_id'     => $sorted[$j]->subject_id,
                                'subject'        => $sorted[$i]->subject,
                                'lesson_id'      => $sorted[$j]->lesson_id,
                                'timeslot_id'    => $sorted[$j]->timeslot_id,
                                'timeslot'       => $sorted[$j]->timeslot,
                            ];
                            break 2; // tylko pierwsza para z odstępem > 1
                        }
                    }
                }

                return $results;
            })
            ->values();

        if($lessons->count()) {
            $problem["Problem_type"] = "Dwie lekcje tego samego przedmiotu nie bezpośrednio po sobie";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson['weekday'];
            $problemDetail["cohort"] = $lesson['cohort'];
            $problemDetail["subject"] = $lesson['subject'];
            $problemDetail["timeslot"] = $lesson['timeslot'];

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function properSubjectTeachers(array $problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->join('weekdays', 'weekdays.id', '=','lessons.weekday_id')
            ->join('timeslots', 'timeslots.id', '=','lessons.timeslot_id')
            ->join('cohorts', 'cohorts.id', '=','lessons.cohort_id')
            ->join('teachers', 'teachers.id', '=','lessons.teacher_id')
            ->join('subjects', 'subjects.id', '=','lessons.subject_id')
            ->join("cohort_subjects", function($join){
                $join->on("cohort_subjects.subject_id", "=", "lessons.subject_id")
                ->on("cohort_subjects.cohort_id", "=", "lessons.cohort_id");
            })
            ->join('teachers as teachers2', 'teachers2.id', '=','cohort_subjects.teacher_id')
            ->where([["lessons.status", "ACTIVE"], ['lessons.plan_id', $plan->id]])
            ->whereColumn('lessons.teacher_id', "!=", 'cohort_subjects.teacher_id')
            ->select(
                'weekdays.name as weekday', 
                'timeslots.start as timeslot', 
                DB::raw("CONCAT(cohorts.level, cohorts.line) as cohort"),
                'subjects.name as subject',
                DB::raw("CONCAT(teachers.first_name, ' ', teachers.last_name) as plan_teacher"),
                DB::raw("CONCAT(teachers2.first_name, ' ', teachers2.last_name) as required_teacher"),
            )
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Lekcje zaplanowane dla innych nauczycieli niż przypisani dla danej klasy";
        }

        foreach($lessons as $lesson) {
            $problemDetail["weekday"] = $lesson->weekday;
            $problemDetail["timeslot"] = $lesson->timeslot;
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["plan_teacher"] = $lesson->plan_teacher;
            $problemDetail["required_teacher"] = $lesson->required_teacher;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }

    private static function amountOfSubjectLessons(array $problems, Plan $plan): array {
        $lessons = DB::table('lessons')
            ->selectRaw("CONCAT(cohorts.level, cohorts.line) AS cohort")
            ->addSelect('subjects.name as subject')
            ->addSelect(DB::raw('COUNT(lessons.subject_id) as plan_sum'))
            ->addSelect('cohort_subjects.amount as required_sum')
            ->join('cohorts', 'cohorts.id', '=', 'lessons.cohort_id')
            ->join('subjects', 'subjects.id', '=', 'lessons.subject_id')
            ->join('cohort_subjects', function ($join) {
                $join->on('cohort_subjects.subject_id', '=', 'lessons.subject_id')
                    ->on('cohort_subjects.cohort_id', '=', 'lessons.cohort_id');
            })
            ->where('lessons.status', 'ACTIVE')
            ->where('lessons.plan_id', 38)
            ->groupBy(
                'lessons.cohort_id',
                'lessons.subject_id',
                'cohorts.level',
                'cohorts.line',
                'subjects.name',
                'cohort_subjects.amount'
            )
            ->havingRaw('COUNT(lessons.subject_id) != cohort_subjects.amount')
            ->get();

        if($lessons->count()) {
            $problem["Problem_type"] = "Liczba lekcji niezgodna z przedmiotami przypisanymi do danej klasy";
        }

        foreach($lessons as $lesson) {
            $problemDetail["cohort"] = $lesson->cohort;
            $problemDetail["subject"] = $lesson->subject;
            $problemDetail["plan_sum"] = $lesson->plan_sum;
            $problemDetail["required_sum"] = $lesson->required_sum;

            $problem["details"][] = $problemDetail;
        }

        if(isset($problem)) {
            $problems[] = $problem;
        }

        return $problems;
    }
}
