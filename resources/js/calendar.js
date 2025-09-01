import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import timelinePlugin from '@fullcalendar/timeline';

document.addEventListener('DOMContentLoaded', function () {
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
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },
         
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '1', classroom: 'sala 101', kohort: 'klasa 1b', subject: 'matematyka', teacher: 'Blaise Pascal', startTime: '08:00', endTime: '08:45' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 1a', subject: 'j. polski', teacher: 'Adam Mickiewicz', startTime: '08:55', endTime: '10:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 3c', subject: 'historia', teacher: 'Jan Sobieski', startTime: '10:45', endTime: '11:30' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'j. angielski', teacher: 'William Shakespeare', startTime: '11:40', endTime: '12:25' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 2b', subject: 'historia', teacher: 'Tadeusz Kościuszko', startTime: '12:55', endTime: '13:40' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'j. niemiecki', teacher: 'Johann Goethe', startTime: '13:50', endTime: '14:35' },
         { day: '1', classroom: 'sala 101', kohort: 'klasa 4a', subject: 'geografia', teacher: 'Edmund Strzelecki', startTime: '14:45', endTime: '15:30' },

         { day: '3', title: 'Szkolenie', startTime: '13:00', endTime: '15:00' }
      ];

      const fullCalendarEvents = rawEvents.map(event => {
         const date = getDateForWeekday(event.day);
         return {
            title: event.startTime + "-" + event.endTime + " | " + event.classroom,
            subtitle1: event.kohort + " | " + event.subject,
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
         slotMaxTime: '18:00:00',
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


