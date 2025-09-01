<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

// use Illuminate\Support\Facades\Auth;

define('GEMINI_API_KEY', 'AIzaSyDzRNOjpiTU0CCms0RGCqxJfhlLA5WQUdw'); // 🔐 Wklej swój klucz
define('MODEL', 'gemini-2.0-flash'); // lub np. 'gemini-1.5-flash'
define('BASEURL', 'https://generativelanguage.googleapis.com/v1');

final readonly class GetChatAnswer
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $prompt = $args["input"]["prompt"];

        $url = BASEURL . "/models/" . MODEL . ":generateContent?key=" . GEMINI_API_KEY;

        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        $response = curl_exec($ch);
        curl_close($ch);

        $responseArray = json_decode($response, true);

        if (!isset($responseArray['candidates'][0]['content']['parts'][0]['text'])) {
            return "Brak odpowiedzi lub błąd.";
        }

        $chatAnswer = $responseArray['candidates'][0]['content']['parts'][0]['text'];

        if (strlen($chatAnswer) >= 1 && substr($chatAnswer, -1) == "\n") {
            $chatAnswer = substr($chatAnswer, 0, -1);
        }

        return $chatAnswer;
    }
}
