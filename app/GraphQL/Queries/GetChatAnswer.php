<?php 
declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Classroom;
use Illuminate\Support\Facades\DB;
use App\Models\Cohort;
use App\Models\CohortSubject;
use App\Models\Season;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timeslot;
use App\Models\Weekday;

// use Illuminate\Support\Facades\Auth;

//GOOGLE GEMINI
// define('API_KEY', env("CHAT_API_KEY_GEMINI")); // 🔐 Wklej swój klucz
// define('MODEL', 'gemini-2.0-flash'); // lub np. 'gemini-1.5-flash'
// define('BASEURL', 'https://generativelanguage.googleapis.com/v1');

//DEEPSEEK
define('API_KEY', env("CHAT_API_KEY_DEEPSEEK")); // 🔐 Wklej swój klucz
define('MODEL', 'deepseek-chat'); // lub 'deepseek-reasoner'
define('BASEURL', 'https://api.deepseek.com');

//ANTHROPIC CLAUDE
// define('API_KEY', env("CHAT_API_KEY_CLAUDE")); // 🔐 Twój klucz API Claude
// define('MODEL', 'claude-sonnet-4-0'); //  'claude-opus-4-1-20250805-thinking-16k'
// define('BASEURL', 'https://api.anthropic.com/v1/messages');

define('MAX_TOKENS', 1024); // Maksymalna liczba tokenów
define('CACHE_DIR', '/tmp/cache/'); // 📂 Katalog do przechowywania cache
define('CACHE_TTL', 3600); // 🕒 Czas życia cache w sekundach (np. 1 godzina)
define('TEMPERATURE', 0.7); // 🌡️ Domyślna temperatura odpowiedzi (0.0–2.0)

final readonly class GetChatAnswer
{
    
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        set_time_limit(180);

        //COHORTS
        // $season = Season::where("status", "ACTIVE")->first();
        
        // if($season) {
        //     $chatAnswer = Cohort::where("status", "ACTIVE")->where("season_id", $season->id)->get();

        //     $chatAnswer = "klasy: " . $chatAnswer->map(function ($cochort) {
        //         return [
        //             'id'    => $cochort->id,
        //             'rocznik'  => $cochort->level,
        //             'grupa' => $cochort->line,
        //             'nazwa' => $cochort->level.$cochort->line,
        //             'sala_id' => $cochort->classroom_id,
        //         ];
        //     })->toJson(JSON_UNESCAPED_UNICODE);
        // }
        // else {
        //     $chatAnswer = null;
        // }
    //END: COHORTS

        //CLASSROOMS
        // $chatAnswer = Classroom::where("status", "ACTIVE")->where("school_id", 1)->get();

        // $chatAnswer = "sale: " . $chatAnswer->map(function ($classroom) {
        //     return collect([
        //         'id'     => $classroom->id,
        //         'piętro' => $classroom->floor,
        //         'nazwa'  => $classroom->name,
        //     ])->when($classroom->subjects->isNotEmpty(), function ($collection) use ($classroom) {
        //         $collection['przedmioty'] = $classroom->subjects->map(function ($subject) {
        //             return [
        //                 'przedmiot_id'    => $subject->id,
        //                 'przedmiot_nazwa' => $subject->name,
        //             ];
        //         })->values()->all();
        //     })->all();
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: CLASSROOMS

        //WEEKDAYS
        // $chatAnswer = Weekday::all();

        // $chatAnswer = "dni_tygodnia: " . $chatAnswer->map(function ($weekday) {
        //     return [
        //         'id'    => $weekday->id,
        //         'nazwa' => $weekday->name,
        //     ];
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: WEEKDAYS

        //TIMESLOTS
        // $chatAnswer = Timeslot::where("status", "ACTIVE")->where("school_id", 1)->get();

        // $chatAnswer = "sloty_czasowe: " . $chatAnswer->map(function ($timeslot) {
        //     return [
        //         'id'    => $timeslot->id,
        //         'start_lekcji' => $timeslot->start,
        //         'koniec_lekcji' => $timeslot->end,
        //     ];
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: TIMESLOTS

        //SUBJECTS
        // $chatAnswer = Subject::where("status", "ACTIVE")->where("school_id", 1)->get();

        // $chatAnswer = "przedmioty: " . $chatAnswer->map(function ($subject) {
        //     return [
        //         'id'    => $subject->id,
        //         'nazwa' => $subject->name,
        //     ];
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: SUBJECTS

        //TEACHERS
        // $chatAnswer = Teacher::where("status", "ACTIVE")->where("school_id", 1)->get();

        // $chatAnswer = "nauczyciele: " . $chatAnswer->map(function ($teacher) {
        //     return [
        //         'id'    => $teacher->id,
        //         'nazwisko' => $teacher->first_name . " " . $teacher->last_name,
        //     ];
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: TEACHERS

        //COHORT_SUBJECTS
        // $chatAnswer = CohortSubject::where("status", "ACTIVE")->where("school_id", 1)->get();

        // $chatAnswer = "nauczyciele: " . $chatAnswer->map(function ($teacher) {
        //     return [
        //         'id'    => $teacher->id,
        //         'nazwisko' => $teacher->first_name . " " . $teacher->last_name,
        //     ];
        // })->toJson(JSON_UNESCAPED_UNICODE);
        //END: COHORT_SUBJECTS

        //COHORT_SUBJECTS
        // $season = Season::where("status", "ACTIVE")->first();
        
        // if($season) {
        //     $cohorts = Cohort::where("status", "ACTIVE")->where("season_id", $season->id)->pluck("id")->toArray();

        //     if($cohorts) {
        //         $chatAnswer = CohortSubject::where("status", "ACTIVE")->whereIn("cohort_id", $cohorts)->get();

        //         $chatAnswer = "klasy_przedmioty: " . $chatAnswer->map(function ($cohortSubject) {
        //             return [
        //                 'klasa_id' => $cohortSubject->cohort_id,
        //                 'klasa_nazwa' => $cohortSubject->cohort_level . $cohortSubject->cohort_line,
        //                 'przedmiot_id' => $cohortSubject->subject_id,
        //                 'przedmiot_nazwa' => $cohortSubject->subject->name,
        //                 'liczba_lekcji_tygodniowo' => $cohortSubject->amount,
        //                 'nauczyciel_id' => $cohortSubject->teacher_id,
        //                 'nauczyciel_nazwisko' => $cohortSubject->teacher->first_name . " " . $cohortSubject->teacher->last_name,
        //             ];
        //         })->toJson(JSON_UNESCAPED_UNICODE);
        //     }
        //     else {
        //         $chatAnswer = "error 1";
        //     }
        // }
        // else {
        //     $chatAnswer = "error 2";
        // }
        //END: COHORT_SUBJECTS











        // $prompt = $args["input"]["prompt"];
        // $temperature = isset($args["input"]["temperature"]) ? (float)$args["input"]["temperature"] : TEMPERATURE; // Dynamiczna temperatura z argumentów lub domyślna

        // $cacheKey = md5($prompt . '|' . $temperature); // 🔑 Generujemy unikalny klucz na podstawie promptu

        // $cacheKey = md5($prompt); // 🔑 Generujemy unikalny klucz na podstawie promptu
        // $cacheFile = CACHE_DIR . $cacheKey . '.cache';

        // $modelShortName = substr(MODEL, 0, 6);
        // if($modelShortName == "gemini") {
        //     $url = BASEURL . "/models/" . MODEL . ":generateContent?key=" . API_KEY;

        //     $data = [
        //         "contents" => [
        //             [
        //                 "parts" => [
        //                     ["text" => $prompt]
        //                 ]
        //             ]
        //         ]
        //     ];

        //     $jsonData = json_encode($data);

        //     $ch = curl_init($url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        //     $response = curl_exec($ch);
        //     curl_close($ch);

        //     $responseArray = json_decode($response, true);

        //     if (!isset($responseArray['candidates'][0]['content']['parts'][0]['text'])) {
        //         return "Brak odpowiedzi lub błąd.";
        //     }

        //     $chatAnswer = $responseArray['candidates'][0]['content']['parts'][0]['text'];
        // }
        // else if($modelShortName == "deepse") {
        //     // 🟢 Sprawdzamy, czy odpowiedź jest w cache (cache hit)
        //     if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL) {
        //         $chatAnswer = file_get_contents($cacheFile);
        //         if ($chatAnswer !== false) {
        //             return $chatAnswer; // Cache hit
        //         }
        //     }

        //     // 🔴 Cache miss - wysyłamy żądanie do API
        //     $url = BASEURL . "/chat/completions";

        //     $data = [
        //         "model" => MODEL,
        //         "messages" => [
        //             [
        //             "role" => "user",
        //             "content" => $prompt
        //             ]
        //         ],
        //         "temperature" => $temperature, // Dodajemy parametr temperature
        //         "stream" => false
        //     ];

        //     $jsonData = json_encode($data);

        //     $ch = curl_init($url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //         'Content-Type: application/json',
        //         'Authorization: Bearer ' . API_KEY
        //     ]);

        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        //     $response = curl_exec($ch);

        //     curl_close($ch);

        //     $responseArray = json_decode($response, true);

        //     if (!isset($responseArray['choices'][0]['message']['content'])) {
        //         return "Brak odpowiedzi lub błąd.";
        //     }

        //     $chatAnswer = $responseArray['choices'][0]['message']['content'];

        //     // 💾 Zapisujemy odpowiedź do cache
        //     if (!is_dir(CACHE_DIR)) {
        //         mkdir(CACHE_DIR, 0755, true);
        //     }
        //     file_put_contents($cacheFile, $chatAnswer);
        // }
        // else if($modelShortName == "claude") {
        //     $url = BASEURL;

        //     $data = [
        //         "model" => MODEL,
        //         "max_tokens" => MAX_TOKENS,
        //         "messages" => [
        //             [
        //                 "role" => "user",
        //                 "content" => $prompt
        //             ],
        //         ],
        //         // "temperature" => $temperature, // Dodajemy parametr temperature
        //         // "stream" => false
        //     ];

        //     $jsonData = json_encode($data);

        //     $ch = curl_init($url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
        //         'Content-Type: application/json',
        //         'x-api-key: ' . API_KEY,
        //         'anthropic-version: 2023-06-01'
        //     ]);
        //     curl_setopt($ch, CURLOPT_TIMEOUT, 120); // ⏱️ Timeout na wszelki wypadek

        //     $response = curl_exec($ch);
        //     curl_close($ch);

        //     $responseArray = json_decode($response, true);

        //     if (!isset($responseArray['content'][0]['text'])) {
        //         return "Brak odpowiedzi lub błąd Claude.";
        //     }

        //     $chatAnswer = $responseArray['content'][0]['text'];
        // }
        // else {
        //     return;
        // }

        // if (strlen($chatAnswer) >= 1 && substr($chatAnswer, -1) == "\n") {
        //     $chatAnswer = substr($chatAnswer, 0, -1);
        // }



        // if(json_validate($chatAnswer)) {
        //     $firstTest = true;
        // }
        // else {
        //     $firstTest = false;
        // }

        // if(!$firstTest) {
        //     $cleanJson = self::extractJsonBlock($chatAnswer);
        // }

        // if($cleanJson) {
        //     $secondTest = true;
        //     $chatAnswer = $cleanJson;
        // }
        // else {
        //     $secondTest = false;
        // }

        return $chatAnswer;
    }

    private static function extractJsonBlock($string) {
        $start = strpos($string, '{');
        $end = strrpos($string, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonCandidate = substr($string, $start, $end - $start + 1);
            if(json_validate($jsonCandidate)) {
                return $jsonCandidate;
            }
        }
        return null;
    }

}
