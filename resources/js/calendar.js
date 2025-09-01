import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';

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
      const jsonString = `
      [
         {
            "title": "Spotkanie zespołu",
            "start": "2025-09-01T10:00:00",
            "end": "2025-09-01T11:00:00"
         },
         {
            "title": "Spotkanie zespołu 2",
            "start": "2025-09-01T10:00:00",
            "end": "2025-09-01T11:00:00"
         },
         {
            "title": "Lunch z klientem",
            "start": "2025-09-02T13:00:00",
            "end": "2025-09-02T14:00:00"
         }
      ]
      `;
      //END: PRZYKŁADOWY JSON Z DANYMI - DO USUNIĘCIA

      const eventsArray = JSON.parse(jsonString);

      const calendar = new Calendar(calendarEl, {
         plugins: [timeGridPlugin],
         initialView: 'timeGridDay',
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
         slotMinTime: '07:00:00',
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
         events: eventsArray,
      });

      calendar.render();
  }
});
