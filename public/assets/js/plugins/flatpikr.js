document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. Basic date picker
    // ==========================
    const datePickers = document.querySelectorAll(".datepicker");
    datePickers.forEach(input => {
        flatpickr(input, {
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: input.value || null
        });
    });

    // ==========================
    // 2. Month picker
    // ==========================
    const monthPickers = document.querySelectorAll(".monthpicker");
    monthPickers.forEach(input => {
        flatpickr(input, {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // Jan, Feb
                    dateFormat: "Y-m",
                    altFormat: "F Y"
                })
            ]
        });
    });

    // ==========================
    // 3. Time picker
    // ==========================
    const timePickers = document.querySelectorAll(".timepicker");
    timePickers.forEach(input => {
        flatpickr(input, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true
        });
    });

    // ==========================
    // 4. Range picker (Start/End date)
    // ==========================
    const rangePickers = document.querySelectorAll(".daterange");
    rangePickers.forEach(input => {
        flatpickr(input, {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });

});
