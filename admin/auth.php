<?php
require_once __DIR__ . '/../config/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_is_logged_in()
{
    return isset($_SESSION['admin_id']);
}

function require_admin()
{
    if (!admin_is_logged_in()) {
        redirect_to('login.php');
    }
}

function current_admin_name()
{
    return isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Administrador';
}

