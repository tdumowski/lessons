<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

// use Illuminate\Support\Facades\Auth;

// define('API_KEY', 'AIzaSyDzRNOjpiTU0CCms0RGCqxJfhlLA5WQUdw'); // 🔐 Wklej swój klucz
// define('MODEL', 'gemini-2.0-flash'); // lub np. 'gemini-1.5-flash'
// define('BASEURL', 'https://generativelanguage.googleapis.com/v1');
define('API_KEY', 'sk-74458534b7d944b6a4da32c960817d36'); // 🔐 Wklej swój klucz
define('MODEL', 'deepseek-chat'); // lub np. 'gemini-1.5-flash'
define('BASEURL', 'https://api.deepseek.com');

final readonly class GetChatAnswer
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $prompt = $args["input"]["prompt"];

        $modelShortName = substr(MODEL, 0, 6);
        if($modelShortName == "gemini") {
            $url = BASEURL . "/models/" . MODEL . ":generateContent?key=" . API_KEY;

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
        }
        else if($modelShortName == "deepse") {
            $url = BASEURL . "/chat/completions";

            $data = [
                "model" => MODEL,
                "messages" => [
                    [
                    "role" => "user",
                    "content" => $prompt
                    ]
                ],
                "stream" => false
            ];

            $jsonData = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . API_KEY
            ]);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

            $response = curl_exec($ch);

            curl_close($ch);

            $responseArray = json_decode($response, true);

            if (!isset($responseArray['choices'][0]['message']['content'])) {
                return "Brak odpowiedzi lub błąd.";
            }

            $chatAnswer = $responseArray['choices'][0]['message']['content'];

        }
        else {
            return;
        }

        if (strlen($chatAnswer) >= 1 && substr($chatAnswer, -1) == "\n") {
            $chatAnswer = substr($chatAnswer, 0, -1);
        }

        return $chatAnswer;
    }
}
