<?php
require_once __DIR__ . '/config/helpers.php';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agendar cita - Hospital San José</title>
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/apple-touch-icon.png">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header class="site-header">
        <a class="brand" href="index.php">
            <span class="brand-mark">
                <img src="assets/img/hospital-logo.png" alt="Logo Hospital San José">
            </span>
            <span>
                <strong>Hospital San José</strong>
                <small>Sistema De Agendamiento de Citas</small>
            </span>
        </a>
        <nav>
            <a href="admin/login.php">Panel administrativo</a>
        </nav>
    </header>

    <main class="appointment-shell">
        <section class="appointment-board" aria-label="Agenda de citas">
            <div class="calendar-panel">
                <div class="panel-heading">
                    <p class="eyebrow">Reserva en línea</p>
                    <h1>Selecciona fecha y hora</h1>
                    <p>Selecciona un día disponible y elige el horario que mejor se acomode a tu visita.</p>
                </div>

                <div class="calendar-toolbar">
                    <button type="button" class="icon-button" id="prevMonth" aria-label="Mes anterior">&lt;</button>
                    <strong id="monthLabel">Mes</strong>
                    <button type="button" class="icon-button" id="nextMonth" aria-label="Mes siguiente">&gt;</button>
                </div>

                <div class="calendar-grid weekdays" aria-hidden="true">
                    <span>DOM</span>
                    <span>LUN</span>
                    <span>MAR</span>
                    <span>MIÉ</span>
                    <span>JUE</span>
                    <span>VIE</span>
                    <span>SÁB</span>
                </div>
                <div class="calendar-grid days" id="calendarDays"></div>

                <div class="timezone-note">
                    <strong>Zona horaria</strong>
                    <span>America/Bogota</span>
                </div>
            </div>

            <aside class="time-panel">
                <div class="selected-date-card">
                    <p class="eyebrow">Fecha seleccionada</p>
                    <h2 id="selectedDateLabel">Selecciona un día</h2>
                </div>

                <div id="statusMessage" class="status-message" role="status"></div>
                <div class="time-list" id="timeList"></div>

                <form id="bookingForm" class="booking-form" autocomplete="off">
                    <input type="hidden" name="appointment_date" id="appointmentDate">
                    <input type="hidden" name="appointment_time" id="appointmentTime">

                    <div class="form-grid">
                        <label>
                            Nombre completo
                            <input type="text" name="patient_name" id="patientName" required>
                        </label>
                        <label>
                            Teléfono
                            <input type="tel" name="phone" id="phone" required>
                        </label>
                        <label>
                            Correo electrónico opcional
                            <input type="email" name="email" id="email">
                        </label>
                    </div>

                    <button type="submit" class="button primary full-width" id="confirmButton" disabled>Confirmar cita</button>
                </form>
            </aside>
        </section>
    </main>

    <script src="assets/js/calendar.js"></script>
</body>
</html>
