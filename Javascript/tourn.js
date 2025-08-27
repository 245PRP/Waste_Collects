 document.addEventListener('DOMContentLoaded', function() {
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'),{
     locale: 'fr',
     initialView: 'dayGridMonth',
     events:'even.php'     
    });
    calendar.render();
  });