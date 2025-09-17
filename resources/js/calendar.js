import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import timelinePlugin from '@fullcalendar/timeline';

document.addEventListener('DOMContentLoaded', function () {


  const btnGeneratePlan = document.getElementById('btnGeneratePlan');
  
  // Sprawdź, czy element istnieje
  if (btnGeneratePlan) {
    btnGeneratePlan.addEventListener('click', async () => {
      
      $(document).Toasts('create', {
         title: 'Generowanie nowego planu rozpoczęte',
         body: 'Po zapisaniu nowego planu otrzymasz maila',
         position: 'bottomRight',
         autohide: true,
         icon: 'fas fa-check',
         class: 'bg-success',
         close: false,
         delay: 5000
      });

      // Pobranie wartości z inputa
      // document.getElementById('chatAnswer').value = 'Czekam na odpowiedź...';
      // const question = document.getElementById('chatQuestion').value;

      // try {
      //   // Wysłanie zapytania GraphQL z input jako obiektem
      //   const { data } = await client.query({
      //     query: CHAT_QUERY,
      //     variables: { input: { prompt: question } }
      //   });

      //   // Wyświetlenie odpowiedzi w textarea
      //   const answer = data.getChatAnswer;
      //   document.getElementById('chatAnswer').value = answer;
      // } catch (error) {
      //   console.error('Błąd podczas wysyłania zapytania GraphQL:', error);
      //   document.getElementById('chatAnswer').value = 'Wystąpił błąd podczas pobierania odpowiedzi.';
      // }
    });
  }





   const calendarEl = document.getElementById('calendar');

   if (calendarEl) {
      //ograniczenie przełączanych dat
      const today = new Date();
      const startOfWeek = new Date(today);
      startOfWeek.setDate(today.getDate() - today.getDay()); // niedziela

      const endOfWeek = new Date(startOfWeek);
      endOfWeek.setDate(startOfWeek.getDate() + 6); // sobota

      //PRZYKŁADOWY JSON Z DANYMI - DO USUNIĘCIA
      const rawEvents = [
    {
      "day": 1,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 1,
      "startTime": "08:55:00",
      "endTime": "09:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 1,
      "startTime": "09:50:00",
      "endTime": "10:35:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. angielski",
      "teacher": "William Shakespeare"
    },
    {
      "day": 1,
      "startTime": "10:45:00",
      "endTime": "11:30:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "matematyka",
      "teacher": "Marian Banach"
    },
    {
      "day": 1,
      "startTime": "11:40:00",
      "endTime": "12:25:00",
      "cohort": "1a",
      "classroom": "101",
      "subject": "fizyka",
      "teacher": "Aleksander Wolszczan"
    },
    {
      "day": 1,
      "startTime": "12:55:00",
      "endTime": "13:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "historia",
      "teacher": "Jan Sobieski"
    },
    {
      "day": 2,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 2,
      "startTime": "08:55:00",
      "endTime": "09:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 2,
      "startTime": "09:50:00",
      "endTime": "10:35:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. angielski",
      "teacher": "William Shakespeare"
    },
    {
      "day": 2,
      "startTime": "10:45:00",
      "endTime": "11:30:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "matematyka",
      "teacher": "Marian Banach"
    },
    {
      "day": 2,
      "startTime": "11:40:00",
      "endTime": "12:25:00",
      "cohort": "1a",
      "classroom": "102",
      "subject": "chemia",
      "teacher": "Maria Skłodowska"
    },
    {
      "day": 2,
      "startTime": "12:55:00",
      "endTime": "13:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "historia",
      "teacher": "Jan Sobieski"
    },
    {
      "day": 3,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "cohort": "1a",
      "classroom": "101",
      "subject": "fizyka",
      "teacher": "Aleksander Wolszczan"
    },
    {
      "day": 3,
      "startTime": "08:55:00",
      "endTime": "09:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. niemiecki",
      "teacher": "Johann Goethe"
    },
    {
      "day": 3,
      "startTime": "09:50:00",
      "endTime": "10:35:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. angielski",
      "teacher": "William Shakespeare"
    },
    {
      "day": 3,
      "startTime": "10:45:00",
      "endTime": "11:30:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "historia",
      "teacher": "Jan Sobieski"
    },
    {
      "day": 3,
      "startTime": "11:40:00",
      "endTime": "12:25:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 4,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "cohort": "1a",
      "classroom": "102",
      "subject": "chemia",
      "teacher": "Maria Skłodowska"
    },
    {
      "day": 4,
      "startTime": "08:55:00",
      "endTime": "09:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. niemiecki",
      "teacher": "Johann Goethe"
    },
    {
      "day": 4,
      "startTime": "09:50:00",
      "endTime": "10:35:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "matematyka",
      "teacher": "Marian Banach"
    },
    {
      "day": 4,
      "startTime": "10:45:00",
      "endTime": "11:30:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "historia",
      "teacher": "Jan Sobieski"
    },
    {
      "day": 4,
      "startTime": "11:40:00",
      "endTime": "12:25:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "WOS",
      "teacher": "Adam Bodnar"
    },
    {
      "day": 4,
      "startTime": "12:55:00",
      "endTime": "13:40:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 5,
      "startTime": "08:00:00",
      "endTime": "08:45:00",
      "cohort": "1a",
      "classroom": "WF",
      "subject": "wf",
      "teacher": "Robert Lewandowski"
    },
    {
      "day": 5,
      "startTime": "08:55:00",
      "endTime": "09:40:00",
      "cohort": "1a",
      "classroom": "WF",
      "subject": "wf",
      "teacher": "Robert Lewandowski"
    },
    {
      "day": 5,
      "startTime": "09:50:00",
      "endTime": "10:35:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "j. polski",
      "teacher": "Juliusz Słowacki"
    },
    {
      "day": 5,
      "startTime": "10:45:00",
      "endTime": "11:30:00",
      "cohort": "1a",
      "classroom": "201",
      "subject": "l. wychowawcza",
      "teacher": "Juliusz Słowacki"
    }
  ];
      // const rawEvents = [
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "1a", "classroom": "307", "subject": "historia", "teacher": "Jan Sobieski"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "1a", "classroom": "307", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "1a", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "1a", "classroom": "206", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "1a", "classroom": "307", "subject": "j. niemiecki", "teacher": "Johann Goethe"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "1a", "classroom": "205", "subject": "matematyka", "teacher": "Marian Banach"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "1a", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "1b", "classroom": "104", "subject": "technika", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "1b", "classroom": "103", "subject": "biologia", "teacher": "Ludwik Hirszfeld"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "1b", "classroom": "103", "subject": "l. wychowawcza", "teacher": "Adam Mickiewicz"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "1b", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "1b", "classroom": "303", "subject": "j. francuski", "teacher": "Marcel Proust"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "1b", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "1b", "classroom": "302", "subject": "matematyka", "teacher": "Marian Banach"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "1c", "classroom": "306", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "1c", "classroom": "207", "subject": "matematyka", "teacher": "Stanisław Ulam"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "1c", "classroom": "205", "subject": "j. angielski", "teacher": "Harold Pinter"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "1c", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "1c", "classroom": "104", "subject": "technika", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "1c", "classroom": "207", "subject": "j. włoski", "teacher": "Umberto Eco"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "1c", "classroom": "207", "subject": "j. polski", "teacher": "Adam Mickiewicz"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "1d", "classroom": "206", "subject": "l. wychowawcza", "teacher": "Harold Pinter"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "1d", "classroom": "104", "subject": "plastyka", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "1d", "classroom": "302", "subject": "j. hiszpański", "teacher": "Gabriel Marquez"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "1d", "classroom": "202", "subject": "j. angielski", "teacher": " Harold Pinter"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "1d", "classroom": "207", "subject": "j. polski", "teacher": "Adam Mickiewicz"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "1d", "classroom": "103", "subject": "biologia", "teacher": "Ludwik Hirszfeld"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "1d", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "2a", "classroom": "201", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "2a", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "2a", "classroom": "201", "subject": "j. niemiecki", "teacher": "Johann Goethe"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "2a", "classroom": "207", "subject": "j. polski", "teacher": "Juliusz Słowacki"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "2a", "classroom": "202", "subject": "matematyka", "teacher": "Marian Banach"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "2a", "classroom": "306", "subject": "l. wychowawcza", "teacher": "Juliusz Słowacki"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "2a", "classroom": "204", "subject": "historia", "teacher": "Jan Sobieski"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "2b", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "2b", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "2b", "classroom": "104", "subject": "plastyka", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "2b", "classroom": "105", "subject": "matematyka", "teacher": "Marian Banach"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "2b", "classroom": "206", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "2b", "classroom": "203", "subject": "j. francuski", "teacher": "Marcel Proust"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "2b", "classroom": "103", "subject": "biologia", "teacher": "Ludwik Hirszfeld"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "2c", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "2c", "classroom": "", "subject": "", "teacher": ""},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "2c", "classroom": "205", "subject": "j. polski", "teacher": "Adam Mickiewicz"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "2c", "classroom": "202", "subject": "j. włoski", "teacher": "Umberto Eco"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "2c", "classroom": "307", "subject": "matematyka", "teacher": "Stanisław Ulam"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "2c", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "2c", "classroom": "301", "subject": "j. angielski", "teacher": " Harold Pinter"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "2c", "classroom": "104", "subject": "plastyka", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "2d", "classroom": "202", "subject": "j. hiszpański", "teacher": "Gabriel Marquez"},
      //    {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "2d", "classroom": "302", "subject": "historia", "teacher": "Jan Sobieski"},
      //    {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "2d", "classroom": "306", "subject": "l. wychowawcza", "teacher": " Harold Pinter"},
      //    {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "2d", "classroom": "104", "subject": "technika", "teacher": "Jan Matejko"},
      //    {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "2d", "classroom": "102", "subject": "chemia", "teacher": "Maria Skłodowska"},
      //    {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "2d", "classroom": "303", "subject": "matematyka", "teacher": "Stanisław Ulam"},
      //    {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "2d", "classroom": "304", "subject": "j. angielski", "teacher": " Harold Pinter"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "3a", "classroom": "204", "subject": "matematyka", "teacher": "Marian Banach"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "3a", "classroom": "202", "subject": "j. niemiecki", "teacher": "Johann Goethe"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "3a", "classroom": "203", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "3a", "classroom": "sala gimnastyczna", "subject": "wf", "teacher": "Robert Lewandowski"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "3a", "classroom": "302", "subject": "l. wychowawcza", "teacher": "Juliusz Słowacki"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "3a", "classroom": "204", "subject": "historia", "teacher": "Jan Sobieski"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "3a", "classroom": "206", "subject": "j. polski", "teacher": "Juliusz Słowacki"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "3b", "classroom": "205", "subject": "j. francuski", "teacher": "Marcel Proust"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "3b", "classroom": "206", "subject": "matematyka", "teacher": "Marian Banach"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "3b", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "3b", "classroom": "303", "subject": "l. wychowawcza", "teacher": "Adam Mickiewicz"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "3b", "classroom": "201", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "3b", "classroom": "sala gimnastyczna", "subject": "wf", "teacher": "Robert Lewandowski"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "3b", "classroom": "306", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "3c", "classroom": "sala gimnastyczna", "subject": "wf", "teacher": "Robert Lewandowski"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "3c", "classroom": "203", "subject": "j. angielski", "teacher": " Harold Pinter"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "3c", "classroom": "303", "subject": "l. wychowawcza", "teacher": "William Shakespeare"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "3c", "classroom": "301", "subject": "historia", "teacher": "Jan Sobieski"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "3c", "classroom": "105", "subject": "matematyka", "teacher": "Stanisław Ulam"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "3c", "classroom": "201", "subject": "j. polski", "teacher": "Adam Mickiewicz"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "3c", "classroom": "305", "subject": "j. włoski", "teacher": "Umberto Eco"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "3d", "classroom": "103", "subject": "biologia", "teacher": "Ludwik Hirszfeld"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "3d", "classroom": "204", "subject": "j. hiszpański", "teacher": "Gabriel Marquez"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "3d", "classroom": "105", "subject": "matematyka", "teacher": "Stanisław Ulam"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "3d", "classroom": "201", "subject": "l. wychowawcza", "teacher": " Harold Pinter"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "3d", "classroom": "301", "subject": "j. angielski", "teacher": " Harold Pinter"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "3d", "classroom": "101", "subject": "fizyka", "teacher": "Aleksander Wolszczan"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "3d", "classroom": "202", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "4a", "classroom": "305", "subject": "l. wychowawcza", "teacher": "Juliusz Słowacki"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "4a", "classroom": "105", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "4a", "classroom": "206", "subject": "j. polski", "teacher": "Juliusz Słowacki"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "4a", "classroom": "203", "subject": "j. niemiecki", "teacher": "Johann Goethe"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "4a", "classroom": "305", "subject": "historia", "teacher": "Jan Sobieski"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "4a", "classroom": "302", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "4a", "classroom": "sala gimnastyczna", "subject": "wf", "teacher": "Robert Lewandowski"},
      //    // {"day": 1, "startTime": "08:00:00", "endTime": "08:45:00", "cohort": "4b", "classroom": "303", "subject": "l. wychowawcza", "teacher": "Adam Mickiewicz"},
      //    // {"day": 1, "startTime": "08:55:00", "endTime": "09:40:00", "cohort": "4b", "classroom": "201", "subject": "j. polski", "teacher": "Juliusz Słowacki"},
      //    // {"day": 1, "startTime": "09:50:00", "endTime": "10:35:00", "cohort": "4b", "classroom": "204", "subject": "historia", "teacher": "Jan Sobieski"},
      //    // {"day": 1, "startTime": "10:45:00", "endTime": "11:30:00", "cohort": "4b", "classroom": "103", "subject": "j. angielski", "teacher": "William Shakespeare"},
      //    // {"day": 1, "startTime": "11:40:00", "endTime": "12:25:00", "cohort": "4b", "classroom": "sala gimnastyczna", "subject": "wf", "teacher": "Robert Lewandowski"},
      //    // {"day": 1, "startTime": "12:55:00", "endTime": "13:40:00", "cohort": "4b", "classroom": "206", "subject": "WOS", "teacher": "Adam Bodnar"},
      //    // {"day": 1, "startTime": "13:50:00", "endTime": "14:35:00", "cohort": "4b", "classroom": "201", "subject": "j. francuski", "teacher": "Marcel Proust"}
      // ];

      // const rawEvents = [
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },
         
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
      //    { day: '1', classroom: 'sala 101', cohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

      //    { day: '3', title: 'Szkolenie', startTime: '13:00', endTime: '15:00' }
      // ];

      const fullCalendarEvents = rawEvents.map(event => {
         const date = getDateForWeekday(event.day);
         return {
            title: event.startTime + "-" + event.endTime + " | " + event.classroom,
            subtitle1: event.cohort + " | " + event.subject,
            subtitle2: event.teacher,
            start: `${date}T${event.startTime}`,
            end: `${date}T${event.endTime}`
         };
      });

      //END: PRZYKŁADOWY JSON Z DANYMI - DO USUNIĘCIA

      const calendar = new Calendar(calendarEl, {
         plugins: [timeGridPlugin],
         initialView: 'timeGridDay',
         // plugins: [timelinePlugin],
         // initialView: 'timelineDay',
         validRange: {
            start: startOfWeek,
            end: endOfWeek
         },
         headerToolbar: {
            left: 'prev,next',
            center: '',
            right: 'timeGridDay,timeGridWeekCustom'
         },
         views: {
            timeGridWeekCustom: {
               type: 'timeGrid',
               duration: { days: 5 }, // poniedziałek–piątek
               buttonText: 'Tydzień (Pn–Pt)'
            }
         },
         slotMinTime: '08:00:00',
         slotMaxTime: '17:00:00',
         hiddenDays: [0, 6], // ukryj niedzielę i sobotę
         firstDay: 1, // tydzień zaczyna się od poniedziałku
         showNonCurrentDates: false, // ukryj daty spoza zakresu
         dayHeaderFormat: { weekday: 'long' }, // tylko nazwy dni tygodnia
         dayCellClassNames: function() {
            return ['fc-no-highlight']; // klasa do usunięcia podświetlenia dnia bieżącego
         },
         nowIndicator: false, // wyłącz wskaźnik aktualnej godziny
         locale: 'pl',
         allDaySlot: false,
         height: 'auto',
         contentHeight: 'auto',
         expandRows: true,
         events: fullCalendarEvents, //eventsArray,
         eventOrder: 'cohort',
         eventContent: function(arg) {
            return {
               html: `
                  <div class="fc-event-custom">
                  <div><strong>${arg.event.title}</strong></div>
                  <div>${arg.event.extendedProps.subtitle1}</div>
                  <div>${arg.event.extendedProps.subtitle2}</div>
                  </div>
               `
            };
         }
      });

      calendar.render();
  }
});

function getDateForWeekday(dayNumber, referenceDate = new Date()) {
  if (dayNumber < 0 || dayNumber > 6) {
    throw new Error('Numer dnia tygodnia musi być w zakresie 0–6 (0 = niedziela, 6 = sobota)');
  }

  const ref = new Date(referenceDate);
  const refDay = ref.getDay(); // np. 1 dla poniedziałku

  // Obliczamy różnicę między początkiem tygodnia a żądanym dniem
  const offset = dayNumber - refDay;

  // Tworzymy nową datę, przesuniętą o offset
  const targetDate = new Date(ref);
  targetDate.setDate(ref.getDate() + offset);

  return targetDate.toISOString().split('T')[0]; // YYYY-MM-DD
}


