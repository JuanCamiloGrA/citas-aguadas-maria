<?php
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(array('success' => false, 'message' => 'Método no permitido.'), 405);
}

$patientName = isset($_POST['patient_name']) ? trim($_POST['patient_name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$date = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$time = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';

if ($patientName === '' || $phone === '' || !is_valid_date_value($date) || !is_valid_time_slot($time)) {
    json_response(array('success' => false, 'message' => 'Completa los datos obligatorios y selecciona un horario válido.'), 422);
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(array('success' => false, 'message' => 'El correo electrónico no tiene un formato válido.'), 422);
}

try {
    $pdo = get_pdo(true);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments
        WHERE appointment_date = ? AND appointment_time = ? AND status = 'reserved'");
    $stmt->execute(array($date, $time . ':00'));

    if ((int) $stmt->fetchColumn() > 0) {
        json_response(array('success' => false, 'message' => 'Ese horario ya fue reservado. Selecciona otro.'), 409);
    }

    $stmt = $pdo->prepare('INSERT INTO appointments
        (patient_name, phone, email, appointment_date, appointment_time, status)
        VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute(array($patientName, $phone, $email === '' ? null : $email, $date, $time . ':00', 'reserved'));

    json_response(array(
        'success' => true,
        'message' => 'Tu cita fue reservada correctamente.',
        'appointment' => array(
            'name' => $patientName,
            'phone' => $phone,
            'email' => $email,
            'date' => $date,
            'time' => $time,
        ),
    ), 201);
} catch (Exception $exception) {
    json_response(array(
        'success' => false,
        'message' => 'No se pudo reservar la cita. Ejecuta install.php primero.',
    ), 500);
}

