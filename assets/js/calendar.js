(function () {
    var monthLabel = document.getElementById('monthLabel');
    var calendarDays = document.getElementById('calendarDays');
    var selectedDateLabel = document.getElementById('selectedDateLabel');
    var timeList = document.getElementById('timeList');
    var statusMessage = document.getElementById('statusMessage');
    var bookingForm = document.getElementById('bookingForm');
    var appointmentDate = document.getElementById('appointmentDate');
    var appointmentTime = document.getElementById('appointmentTime');
    var confirmButton = document.getElementById('confirmButton');

    var today = new Date();
    today.setHours(0, 0, 0, 0);

    var currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    var selectedDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    var selectedTime = '';

    function pad(value) {
        value = String(value);
        return value.length === 1 ? '0' + value : value;
    }

    function toDateValue(date) {
        return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate());
    }

    function formatHumanDate(date) {
        return date.toLocaleDateString('es-CO', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatTime12h(time) {
        var parts = time.split(':');
        var hour = parseInt(parts[0], 10);
        var minutes = parts[1] || '00';
        var suffix = hour >= 12 ? 'PM' : 'AM';
        var displayHour = hour % 12;

        if (displayHour === 0) {
            displayHour = 12;
        }

        return displayHour + ':' + minutes + ' ' + suffix;
    }

    function showStatus(message, type) {
        statusMessage.textContent = message;
        statusMessage.className = 'status-message is-visible ' + type;
    }

    function clearStatus() {
        statusMessage.textContent = '';
        statusMessage.className = 'status-message';
    }

    function renderCalendar() {
        calendarDays.innerHTML = '';
        selectedTime = '';
        appointmentTime.value = '';
        confirmButton.disabled = true;

        var monthName = currentMonth.toLocaleDateString('es-CO', {
            month: 'long',
            year: 'numeric'
        });
        monthLabel.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

        var firstDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
        var lastDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);

        for (var empty = 0; empty < firstDay.getDay(); empty++) {
            var spacer = document.createElement('span');
            spacer.className = 'day-empty';
            calendarDays.appendChild(spacer);
        }

        for (var day = 1; day <= lastDay.getDate(); day++) {
            var date = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), day);
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'day-cell';
            button.textContent = day;

            if (date < today) {
                button.disabled = true;
            } else {
                button.classList.add('has-availability');
            }

            if (toDateValue(date) === toDateValue(selectedDate)) {
                button.classList.add('is-selected');
            }

            button.addEventListener('click', (function (pickedDate) {
                return function () {
                    selectedDate = pickedDate;
                    renderCalendar();
                    loadAvailability();
                };
            })(date));

            calendarDays.appendChild(button);
        }
    }

    function renderSlots(slots) {
        timeList.innerHTML = '';
        selectedTime = '';
        appointmentTime.value = '';
        confirmButton.disabled = true;

        if (!slots.length) {
            showStatus('No hay horarios disponibles para esta fecha.', 'warning');
            return;
        }

        clearStatus();

        slots.forEach(function (slot) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'time-button';
            button.textContent = formatTime12h(slot);

            button.addEventListener('click', function () {
                var buttons = timeList.querySelectorAll('.time-button');
                for (var index = 0; index < buttons.length; index++) {
                    buttons[index].classList.remove('is-selected');
                }

                selectedTime = slot;
                appointmentTime.value = slot;
                confirmButton.disabled = false;
                button.classList.add('is-selected');
                showStatus('Horario seleccionado: ' + formatTime12h(slot) + '. Completa tus datos para confirmar.', 'info');
            });

            timeList.appendChild(button);
        });
    }

    function loadAvailability() {
        var dateValue = toDateValue(selectedDate);
        appointmentDate.value = dateValue;
        selectedDateLabel.textContent = formatHumanDate(selectedDate);
        timeList.innerHTML = '';
        showStatus('Consultando horarios disponibles...', 'info');

        return fetch('api/availability.php?date=' + encodeURIComponent(dateValue))
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    showStatus(data.message || 'No se pudo cargar la disponibilidad.', 'error');
                    return;
                }

                renderSlots(data.slots || []);
            })
            .catch(function () {
                showStatus('No se pudo cargar la disponibilidad. Revisa que la app esté instalada.', 'error');
            });
    }

    function moveMonth(offset) {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + offset, 1);
        selectedDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);

        if (selectedDate < today) {
            selectedDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        }

        renderCalendar();
        loadAvailability();
    }

    document.getElementById('prevMonth').addEventListener('click', function () {
        moveMonth(-1);
    });

    document.getElementById('nextMonth').addEventListener('click', function () {
        moveMonth(1);
    });

    bookingForm.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!selectedTime) {
            showStatus('Selecciona un horario antes de confirmar.', 'warning');
            return;
        }

        confirmButton.disabled = true;
        showStatus('Reservando tu cita...', 'info');

        fetch('api/book.php', {
            method: 'POST',
            body: new FormData(bookingForm)
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (!data.success) {
                    showStatus(data.message || 'No se pudo reservar la cita.', 'error');
                    confirmButton.disabled = false;
                    return;
                }

                var appointment = data.appointment;
                var confirmation = 'Cita confirmada para ' + formatHumanDate(selectedDate) + ' a las ' + formatTime12h(appointment.time) + '. Paciente: ' + appointment.name + '.';
                bookingForm.reset();
                appointmentDate.value = toDateValue(selectedDate);
                appointmentTime.value = '';
                selectedTime = '';
                loadAvailability().then(function () {
                    showStatus(confirmation, 'info');
                });
            })
            .catch(function () {
                showStatus('No se pudo reservar la cita. Intenta nuevamente.', 'error');
                confirmButton.disabled = false;
            });
    });

    renderCalendar();
    loadAvailability();
})();
