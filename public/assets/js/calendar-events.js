document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
    plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    //defaultDate: '2019-08-12',
    navLinks: true, // can click day/week names to navigate views

    weekNumbers: false,
    weekNumbersWithinDays: true,
    //weekNumberCalculation: 'ISO',

    editable: false,
    eventLimit: true, // allow "more" link when too many events
    events: [
            {
                title: 'Meeting',
                start: '2019-09-01T12:00:00',
                end: '2019-09-03T12:00:00',
                url: '#',
                className: 'bg-danger'
            },
            {
                title: 'All Day Event',
                start: '2019-09-04',
                url: '#',
                className: 'bg-success'
            },
            {
                title: 'No work',
                start: '2019-09-04',
                url: '#',
                className: 'bg-danger'
            },
            {
                title: 'Meeting',
                start: '2019-09-23',
                url: '#',
                className: 'bg-success'
            },
            {
                title: 'Event',
                start: '2019-09-23T06:00:00',
                end: '2019-09-23T17:00:00',
                url: '#',
                className: 'bg-info'
            },
            {
                title: 'Event',
                start: '2019-09-24T06:00:00',
                url: '#',
                className: 'bg-info'
            },
            {
                title: 'Event',
                start: '2019-09-25T06:00:00',
                url: '#',
                className: 'bg-info'
            },
            {
                title: 'Event',
                start: '2019-09-26T06:00:00',
                url: '#',
                className: 'bg-info'
            }
        ]
    });

    calendar.render();
});