<?php
require_once __DIR__ . '/database.php';

function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function available_time_slots()
{
    return array('08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00');
}

function is_valid_date_value($date)
{
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }

    $parts = explode('-', $date);
    return checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0]);
}

function is_valid_time_slot($time)
{
    return in_array($time, available_time_slots(), true);
}

function format_time_12h($time)
{
    $timestamp = strtotime($time);

    if ($timestamp === false) {
        return $time;
    }

    return date('g:i A', $timestamp);
}

function json_response($data, $statusCode)
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function redirect_to($path)
{
    header('Location: ' . $path);
    exit;
}
