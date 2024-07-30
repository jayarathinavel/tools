$(document).ready(function () {
    // Fetch available and booked dates from the backend
    $.ajax({
        type: 'POST',
        url: '../handlers/appointment/check_availability.php',
        success: function (response) {
            var data = JSON.parse(response);
            var availableDates = data.available_dates;
            var bookedDates = data.booked_dates;
            var bookedSessions = data.booked_sessions;
            var disabledDates = data.disabled_dates; // Retrieve disabled dates
            configureDatepicker(availableDates, bookedDates, bookedSessions, disabledDates);
        }
    });
});

function formatDate(date) {
    var year = date.getFullYear();
    var month = (date.getMonth() + 1).toString().padStart(2, '0'); // Add leading zero if needed
    var day = date.getDate().toString().padStart(2, '0'); // Add leading zero if needed
    return year + '-' + month + '-' + day;
}

function configureDatepicker(availableDates, bookedDates, bookedSessions, disabledDates) {
    var datepicker = $('<div class="datepicker-container"></div>').appendTo('#datepickerContainer').datepicker({
        format: 'yyyy-mm-dd',
        startDate: new Date(),
        autoclose: true,
        beforeShowDay: function (date) {
            var formattedDate = formatDate(date);
            var isBooked = bookedDates.indexOf(formattedDate) !== -1;

            // Check if all sessions for the date are booked
            var allSessionsBooked = isAllSessionsBooked(formattedDate, bookedSessions);

            // Check if the date should be disabled
            var isDisabled = disabledDates.indexOf(formattedDate) !== -1;

            // Customize the datepicker to gray out booked dates
            return {
                classes: allSessionsBooked || isDisabled ? 'booked-day' : '',
                tooltip: allSessionsBooked || isDisabled  ? 'Not Available' : ''
            };
        }
    });

    // Disable the sessions form group initially
    $('.form-group-session').addClass('disabled-div');

    datepicker.on('changeDate', function (e) {
        $('#appointment_date').val(formatDate(e.date));

        // Enable the sessions form group when a date is selected
        $('.form-group-session').removeClass('disabled-div');

        // To clear the selected values of radio buttons
        var radioButtons = document.querySelectorAll('input[type="radio"][name="session"]');
        radioButtons.forEach(function(radioButton) {
            radioButton.checked = false;
        });


        // Disable booked sessions for the selected date
        var formattedDate = formatDate(e.date);
        var bookedSessionsForDate = bookedSessions[formattedDate];

        $('input[type=radio][name=session]').each(function () {
            var sessionValue = $(this).val();
            if (bookedSessionsForDate && bookedSessionsForDate.indexOf(sessionValue) !== -1) {
                $(this).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
            }
        });
    });
}

function isAllSessionsBooked(date, bookedSessions) {
    if (bookedSessions && bookedSessions[date]) {
        var allSessions = ['Morning', 'Afternoon', 'Evening'];
        return allSessions.every(function (session) {
            return bookedSessions[date].indexOf(session) !== -1;
        });
    }
    return false;
}
