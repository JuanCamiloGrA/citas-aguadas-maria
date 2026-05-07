<?php
require_once __DIR__ . '/../config/helpers.php';

$date = isset($_GET['date']) ? trim($_GET['date']) : '';

if (!is_valid_date_value($date)) {
    json_response(array('success' => false, 'message' => 'Fecha inválida.'), 422);
}

try {
    $pdo = get_pdo(true);
    $slots = available_time_slots();

    $stmt = $pdo->prepare("SELECT TIME_FORMAT(appointment_time, '%H:%i') AS appointment_time
        FROM appointments
        WHERE appointment_date = ? AND status = 'reserved'");
    $stmt->execute(array($date));
    $reserved = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $available = array_values(array_diff($slots, $reserved));

    json_response(array(
        'success' => true,
        'date' => $date,
        'slots' => $available,
        'reserved' => array_values($reserved),
    ), 200);
} catch (Exception $exception) {
    json_response(array(
        'success' => false,
        'message' => 'No se pudo consultar la disponibilidad. Ejecuta install.php primero.',
    ), 500);
}

