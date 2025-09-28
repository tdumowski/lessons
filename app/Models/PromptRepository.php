<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromptRepository extends Model
{
    public static function getPrompt_1_Initial(): String {
        return 
            "Jesteś specjalistą ds. planowania.
            Twoim zadaniem jest opracowanie planu lekcji w szkole.
            Szczegóły dotyczące zadania, tj. zasoby, które należy uwzględnić oraz reguły, którymi należy kierować się podczas opracowywania planu znajdują się poniżej.

            Opis zadania składa się z 4 części: 
            1) OGÓLNY OPIS (zawiera ideę zadania, ogólnie przedstawia kontekst i podstawowe zależności pomiędzy obiektami będącymi przedmiotem reguł itd.)
            2) ZBIORY DANYCH (zawiera szczegółowe informacje nt. obiektów)
            3) REGUŁY (zawiera szczegółowe zasady, według których zadanie powinno zostać wykonane)
            4) OPIS OCZEKIWANEJ ODPOWIEDZI (zawiera informacje dot. formatu, w jakim powinny zostać zwrócone dane, będące rozwiązaniem zadania).
            5) ZAKRES PRZETWARZANYCH DANYCH (zawiera informacje dot. klas, dla których ma zostać wygenerowany plan).
            6) WYKLUCZENIE KOLIZJI (zawiera już wygenerowany planu dla innych klas w celu uniknięcia kolizji z aktualnie generowanym planem).
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
            END ";
    }

    public static function getPrompt_2_GeneralDatasets(): String {
        $prompt = "ZBIORY DANYCH: START ";

        //CLASSROOMS
        $classrooms = Classroom::where("status", "ACTIVE")->where("school_id", 1)->get();

        $prompt .= "sale: " . $classrooms->map(function ($classroom) {
            return collect([
                'id'     => $classroom->id,
                'piętro' => $classroom->floor,
                'nazwa'  => $classroom->name,
            ])->when($classroom->subjects->isNotEmpty(), function ($collection) use ($classroom) {
                $collection['przedmioty'] = $classroom->subjects->map(function ($subject) {
                    return [
                        'przedmiot_id'    => $subject->id,
                        'przedmiot_nazwa' => $subject->name,
                        'przedmiot_wyłączność' => $subject->exclusive,
                    ];
                })->values()->all();
            })->all();
        })->toJson(JSON_UNESCAPED_UNICODE) . "; ";
        //END: CLASSROOMS

        //WEEKDAYS
        $weekdays = Weekday::all();

        $prompt .= "dni_tygodnia: " . $weekdays->map(function ($weekday) {
            return [
                'id'    => $weekday->id,
                'nazwa' => $weekday->name,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE) . "; ";
        //END: WEEKDAYS

        //TIMESLOTS
        $timeslots = Timeslot::where("status", "ACTIVE")->where("school_id", 1)->get();

        $prompt .= "sloty_czasowe: " . $timeslots->map(function ($timeslot) {
            return [
                'id'    => $timeslot->id,
                'start_lekcji' => $timeslot->start,
                'koniec_lekcji' => $timeslot->end,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE) . "; ";
        //END: TIMESLOTS

        //TEACHERS
        $teachers = Teacher::where("status", "ACTIVE")->where("school_id", 1)->get();

        $prompt .= "nauczyciele: " . $teachers->map(function ($teacher) {
            return [
                'id'    => $teacher->id,
                'nazwisko' => $teacher->first_name . " " . $teacher->last_name,
            ];
        })->toJson(JSON_UNESCAPED_UNICODE) . "; ";
        //END: TEACHERS

    return $prompt;
    }

    public static function getPrompt_3_Rules(): String {
        return 
            "REGUŁY:
            START
            Reguła 1: Wszystkie poniższe reguły obowiązują łącznie, żadna z reguł nie jest ani ważniejsza ani mniej ważna od pozostałych i wszystkie powinny zostać wzięte pod uwagę podczas analizy danych i opracowywania harmonogramu.
            Interpretacja: Nie można stosować jednej reguły w oderwaniu od pozostałych. Harmonogram musi spełniać wszystkie warunki jednocześnie.
            UWAGA: Przykłady podane pod każdą z reguł służą tylko ilustracji efektu działania reguły. Dane (np. nazwy przedmiotów, numery sal itd.) zawarte w przykładach nie mają odzwierciedlenia
            w realnych danych zawartych w zbiorach. Do wykonania zadania należy użyć wyłącznie danych ze zbiorów.

            Reguła 2: Sloty nie muszą być w pełni wykorzystane.
            Interpretacja: Klasa może zacząć lekcje później niż w pierwszym slocie i zakończyć wcześniej niż w ostatnim.
            Przykład: Klasa 3a może mieć lekcje od 9:50 do 11:40, pomijając wcześniejsze i/lub późniejsze godziny.

            Reguła 3: Lekcje dla danej klasy muszą być w kolejno następujących po sobie slotach czasowych.
            Interpretacja: Jeśli klasa ma więcej niż jedną lekcję danego dnia, muszą one być w kolejnych godzinach.
            Przykład: Lekcje dl klasy 3a w slotach 2 i 3 są prawidłwe, ale w slotach 2 i 4 z pominięciem 3 są niedozwolone.

            Reguła 4: Przedmioty profilowe tylko w salach profilowych.
            Interpretacja: Przedmiot przypisany do sali profilowej musi być realizowany wyłącznie w tej sali.
            Przykład: Jeśli fizyka jest przypisana do sali 501, to lekcje fizyki mogą odbywać się wyłącznie w sali 501 ale jeśli chemia jest przypisana do sal 502 i 503 to lekcje chemii mogą odbywac się w dowolnej z tych sal i w żadnej innej.

            Reguła 5: Priorytet sal profilowych dla przedmiotów przypisanych do danej sali.
            Interpretacja: Jeśli w danym slocie o dostęp do sali profilowej ubiegają się lekcje z różnych przedmiotów a wśród nich jest przedmiot przypisany do tej sali, to ten własnie przedmiot ma pierwszeństwo.
            Przykład: Jeśli do sali 501 przypisana jest fizyka i o dostęp w danym slocie ubiegają się matematyka, fizyka i historia, to fizyka ma pierwszeństwo przed innymi przedmiotami.

            Reguła 6: Sala profilowa (o ile o dostęp do niej w danym slocie ubiegają sie inne przedmioty) może być użyta przez inne przedmioty, ale tylko nieprofilowe. W tej sytuacji salę można zarezerwować dla losowo wybranego przedmiotu z tych, które o daną salę się ubiegają.
            Interpretacja: Jeśli sala profilowa jest wolna, może być użyta wyłącznie przez przedmiot nieprofilowy.
            Ograniczenie: W sali profilowej nie można prowadzić lekcji z innych przedmiotów profilowych.
            Przykład: W sali chemicznej (czyli profilowej) może odbyć się lekcja WOS (bo WOS nie ma przypisanej żadnej sali więc jest przedmioten nieprofilowym), ale nie fizyki (bo do fizyki przypisana jest inna pracownia więc jest przedmiotem profilowym). Jeżeli o dostęp do sali chemicznej ubiegają się dwa przedmioty nieprofilowe (np. historia i matematyka) to przedmiot ten należy wylosować.

            Reguła 7: Priorytet przypisanej sali dla klasy dla przedmiotów nieprofilowych.
            Interpretacja: Klasa ma przypisaną stałą salę (pole 'sala_id' w zbiorze 'klasy' ma id sali). W tym przypadku przedmioty nieprofilowe odbywają się w tej przypisanej sali i ta klasa ma do tej sali absolutny priorytet.
            Przykład: Klasa 3c ma przypisaną stałą salę nr 402. J. Polski, historia i matematyka to przedmioty nieprofilowe więc dla klasy 3c te lekcje organizowane powinny być wyłącznie w sali 402.

            Reguła 8: Dowolny wybór sali dla przedmiotów nieprofilowych dla klasy bez sali.
            Interpretacja: Klasa nie ma przypisanej stałej sali (pole 'sala_id' w zbiorze 'klasy' ma wartość NULL). W tym przypadku przedmioty nieprofilowe odbywają się w dowolnej, pierwszej wolnej nieprofilowej sali.
            Przykład: Klasa 3c ma przypisaną stałą salę nr 402. J. Polski, historia i matematyka to przedmioty nieprofilowe więc dla klasy 3c te lekcje organizowane powinny być wyłącznie w sali 402.

            Reguła 9: W sali z przypisanym przedmiotem na wyłączność, mogą odbywać się wyłącznie lekcje z przedmiotów przypisanych do tej sali.

            Reguła 10: W danym dniu w jednym slocie czasowym w jednej sali może odbywać się dokładnie jedna lekcja, która spełnia jednocześnie wszystkie poniższe warunki:
                a) Dotyczy jednej klasy
                b) Dotyczy jednego przedmiotu
                c) Prowadzona jest przez jednego nauczyciela
                d) Odbywa się w jednej sali
                e) Nie może być żadnej innej lekcji w tej samej sali w tym samym czasie — niezależnie od klasy, przedmiotu czy nauczyciela
            Przykład: W czwartek klasa 1a ma fizykę w sali 201 o 8:00, więc żadna inna lekcja nie może się tam odbywać tego dnia o tej godzinie.

            Reguła 11: Nauczyciel może prowadzić tylko jedną lekcję w jednym slocie w danym dniu.
            Interpretacja: Nauczyciel nie może być przypisany do dwóch klas w tym samym czasie.
            Przykład: We wtorek Maria Skłodowska nie może prowadzić chemii dla 1b i 1c o 9:50.

            Reguła 12: Klasa może mieć tylko jedną lekcję w jednym slocie.
            Warunek: Klasa nie może mieć dwóch lekcji jednocześnie.
            Przykład: Klasa 1a nie może mieć matematyki i historii we wtorek o 10:45.

            Reguła 13: Dla danej klasy maksymalnie 2 lekcje tego samego przedmiotu dziennie.
            Przykład: Jeśli klasa 1c ma przewidziane 3 lekcje fizyki w tygodniu to może mieć po jednej lekcji fizyki w poniedziałek, we wtorek i czwartek lub 1 lekcję w poniedziałek i 2 lekcje w czwartek. 3 lub więcej lekcji fizyki dla klasy 1c w jednym dniu jest niedozwolone.

            Reguła 14: Jeśli danego dnia dla danej klasy zostały zaplanowane dwie lekcje tego samego przedmiotu to muszą odbyć się w kolejno następujących po sobie slotach.
            Interpretacja: Dwie lekcje tego samego przedmiotu dla danej klasy tego samego dnia muszą na równi spełnić poniższe warunki:
                a) Muszą być w tej samej sali.
                b) Muszą być w bezpośrednio następujących slotach.
            W tej sytuacji zaplanowanie takich lekcji w różnych salach i/lub w dwóch różnych slotach jest niedozwolone.
            Przykład: Fizyka w środę dla klasy 2a o 8:00 i 8:55 w sali 201 → prawidłowo. Fizyka w środę dla klasy 2a o 8:00 i 9:50 → niedopuszczalne.

            Reguła 15: Lekcje powinny być równomiernie rozłożone na cały tydzień.
            Interpretacja: Nie należy kumulować wszystkich lekcji danego przedmiotu w jednym dniu.
            Przykład: Język polski dla klasy 1c 7x w tygodniu → lepiej rozłożyć po 1–2 lekcje dziennie.
            END

            OPIS OCZEKIWANEJ ODPOWIEDZI:
            START
            Harmonogram posortuj wg. klasy i slotów czasowych.
            Po wygenerowaniu harmonogramu przedstaw go w formacie JSON o strukturze:
            [{'dzien_id': int, 'slot_id': int, 'sala_id': int, 'klasa_id': int, 'przedmiot_id': int, 'nauczyciel_id': int}]

            W JSON w poszczególnych polach umieść dane wg. schematu:
            - 'dzien_id' => 'id' ze zbioru 'dni_tygodnia'
            - 'slot_id' => 'id' ze zbioru 'sloty_czasowe'
            - 'sala_id' => 'id' ze zbioru 'sale'
            - 'klasa_id' => 'id' ze zbioru 'klasy'
            - 'przedmiot_id' => 'id' ze zbioru 'przedmioty'
            - 'nauczyciel_id' => 'id' ze zbioru 'nauczyciele'

            Jako odpowiedź powinien zostać zwrócony wyłącznie wynikowy, czysty JSON bez żadnych dodatkowych znaków.
            END
        ";
    }

    public static function getPrompt_4_Scope(Cohort $cohort): String {
        return 
            "ZAKRES PRZETWARZANYCH DANYCH:
            START
            Bardzo starannie przeanalizuj zbiory danych i reguły. To bardzo ważne.
            Wygeneruj harmonogram lekcji dla klasy o id = " . $cohort->id . " (" . $cohort->level . $cohort->line . ") na cały tydzień z uwzględnieniem powyższych reguł i danych.
            END
        ";
    }

    public static function getPrompt_5_Colisions(string $newPlan): String {
        return 
            "WYKLUCZENIE KOLIZJI:
            START
            Poniżej znajduje się wcześniej wygenerowany plan dla innych klas.
            Opracuj nowy plan tak aby unikał on kolizji z wcześniej wygenerowanym planem.
            Dla aktualnie analizowanej klasy niedopuszczalne jest:
            1) zaplanowanie jej lekcji w tym samym dniu (dzień_id), tym samym slocie (slot_id) i w tej samej sali (sala_id) ORAZ
            2) zaplanowanie jej lekcji w tym samym dniu (dzień_id), tym samym slocie (slot_id) i z tym samym nauczycielem (nauczyciel)
            co we wcześniej wygenerowanym planem.
            Wcześniej wygenerowany plan: {$newPlan}.
            END
        ";
    }
}
