<?php
require_once __DIR__ . '/auth.php';

$_SESSION = array();
session_destroy();

redirect_to('login.php');

