<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// //GOOGLE GEMINI
// // define('API_KEY', env("CHAT_API_KEY_GEMINI")); // 🔐 Wklej swój klucz
// // define('MODEL', 'gemini-2.0-flash'); // lub np. 'gemini-1.5-flash'
// // define('BASEURL', 'https://generativelanguage.googleapis.com/v1');

// //DEEPSEEK
// define('API_KEY', env("CHAT_API_KEY_DEEPSEEK")); // 🔐 Wklej swój klucz
// define('MODEL', 'deepseek-chat'); // lub 'deepseek-reasoner'
// define('BASEURL', 'https://api.deepseek.com');

// //ANTHROPIC CLAUDE
// // define('API_KEY', env("CHAT_API_KEY_CLAUDE")); // 🔐 Twój klucz API Claude
// // define('MODEL', 'claude-sonnet-4-0'); //  'claude-opus-4-1-20250805-thinking-16k'
// // define('BASEURL', 'https://api.anthropic.com/v1/messages');

// //COMMON OPTIONS
// define('MAX_TOKENS', 1024); // Maksymalna liczba tokenów
// define('CACHE_DIR', '/tmp/cache/'); // 📂 Katalog do przechowywania cache
// define('CACHE_TTL', 3600); // 🕒 Czas życia cache w sekundach (np. 1 godzina)
// define('TEMPERATURE', 0.7); // 🌡️ Domyślna temperatura odpowiedzi (0.0–2.0)

class PromptRepository extends Model
{
    public static function getPrompt(Cohort $cohort) {
        // set_time_limit(180);

        $prompt = self::getPrompt_1_Initial();

        $prompt .= self::getPrompt_2_Datasets($cohort);

        return $prompt;


    }

    private static function getPrompt_1_Initial(): String {
        return 
            "Jesteś specjalistą ds. planowania.
            Twoim zadaniem jest opracowanie planu lekcji w szkole.
            Szczegóły dotyczące zadania, tj. zasoby, które należy uwzględnić oraz reguły, którymi należy kierować się podczas opracowywania planu znajdują się poniżej.

            Opis zadania składa się z 4 części: 
            1) OGÓLNY OPIS (zawiera ideę zadania, ogólnie przedstawia kontekst i podstawowe zależności pomiędzy obiektami będącymi przedmiotem reguł itd.)
            2) ZBIORY DANYCH (zawiera szczegółowe informacje nt. obiektów)
            3) REGUŁY (zawiera szczegółowe zasady, według których zadanie powinno zostać wykonane)
            4) OPIS OCZEKIWANEJ ODPOWIEDZI (zawiera informacje dot. formatu, w jakim powinny zostać zwrócone dane, będące rozwiązaniem zadania). 
            Każda z części zaczyna się wyrazem START i kończy wyrazem END.

            OGÓLNY OPIS:
            START
            Zadanie polega na wygenerowaniu harmonogramu lekcji dla szkoły.

            Szkoła prowadzi lekcje dla uczniów podzielonych na klasy.
            Wykaz klas zawiera zbiór 'klasy'.

            Lekcje z danego przedmiotu prowadzą nauczyciele.
            Wykaz nauczycieli zawiera zbiór 'nauczyciele'.

            Lekcje odbywają się w określonych dniach tygodnia.
            Wykaz dni tygodnia zawiera zbiór 'dni_tygodnia'.

            Lekcje odbywają się w określonych slotach czasowych.
            Wykaz slotów czasowych zawiera zbiór 'sloty_czasowe'. 
            Godzina rozpoczęcia lekcji oznaczona jest w polu 'start_lekcji' a koniec lekcji w polu 'koniec_lekcji'.
            Poza slotami wymienionymi w zbiorze 'sloty_czasowe' lekcje nie mogą się odbywać.

            Każda lekcja poświęcona jest jednemu przedmiotowi.
            Wykaz przedmiotów zawiera zbiór 'przedmioty'.
            Przedmioty dzielą się na profilowe i nieprofilowe.
            Przedmioty profilowe to te, których nazwa zawarta jest w polu 'przedmiot_nazwa' w zbiorze 'sale'.
            Przedmioty nieprofilowe to te, których nazwa nie jest zawarta w polu 'przedmiot_nazwa' w zbiorze 'sale'.

            Lekcje odbywają się w salach.
            Wykaz sal zawiera zbiór 'sale'.
            Sale dzielą się na profilowe i nieprofilowe.
            Sale profilowe to te, które w zbiorze 'sale' mają pole o nazwie 'przedmiot' zawierające dane o jednym lub więcej przypisanych przedmiotach.
            Sale nieprofilowe to te, które nie mają przypisanego przedmiotu.

            Każda klasa ma określoną liczbę lekcji w tygodniu i każdą lekcję w danej klasie prowadzi przypisany do niej nauczyciel.
            Wykaz obowiązkowej tygodniowej liczby lekcji z danego przedmiotu oraz przypisany do każdej lekcji i klasy nauczyciel zawiera zbiór 'klasy_przedmioty'.
            Liczba lekcji w tygodniu dla danego przedmiotu i dla danej klasy określa pole 'liczba_lekcji_tygodniowo' w zbiorze 'klasy_przedmioty'.

            Każda lekcja zawiera następujące parametry: klasę, przedmiot, nauczyciela, salę, numer dnia tygodnia i slot czasowy.

            Pojęcia 'lekcja' i 'slot czasowy' nie są tożsame. Lekcja przypisywana jest do slotu czasowego. Pojęcie np. 'pierwsza lekcja' nie oznacza pierwszego slotu czasowego.
            END";
    }

    private static function getPrompt_2_Datasets(Cohort $cohort): String {
        $prompt = "ZBIORY DANYCH: START";


                klasy: [
                { "id": 1, "rocznik": 1, "grupa": "a", "nazwa": "1a", sala_id: 6 },
                { "id": 2, "rocznik": 1, "grupa": "b", "nazwa": "1b", sala_id: 7 },
                { "id": 3, "rocznik": 1, "grupa": "c", "nazwa": "1c", sala_id: 8 },
                { "id": 4, "rocznik": 1, "grupa": "d", "nazwa": "1d", sala_id: 9 },
                { "id": 5, "rocznik": 2, "grupa": "a", "nazwa": "2a", sala_id: 10 },
                { "id": 6, "rocznik": 2, "grupa": "b", "nazwa": "2b", sala_id: 11 },
                { "id": 7, "rocznik": 2, "grupa": "c", "nazwa": "2c", sala_id: 12 },
                { "id": 8, "rocznik": 2, "grupa": "d", "nazwa": "2d", sala_id: 13 },
                { "id": 9, "rocznik": 3, "grupa": "a", "nazwa": "3a", sala_id: 14 },
                { "id": 10, "rocznik": 3, "grupa": "b", "nazwa": "3b", sala_id: 15 },
                { "id": 11, "rocznik": 3, "grupa": "c", "nazwa": "3c", sala_id: 16 },
                { "id": 12, "rocznik": 3, "grupa": "d", "nazwa": "3d", sala_id: 17 },
                { "id": 13, "rocznik": 4, "grupa": "a", "nazwa": "4a", sala_id: 18 },
                { "id": 14, "rocznik": 4, "grupa": "b", "nazwa": "4b", sala_id: 19 },
                { "id": 15, "rocznik": 4, "grupa": "c", "nazwa": "4c", sala_id: 20 },
                { "id": 16, "rocznik": 4, "grupa": "d", "nazwa": "4d", sala_id: 21 }
                ]"
    }

    return $prompt;
}
