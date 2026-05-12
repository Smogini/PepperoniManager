<?php
require_once 'DatabaseHelper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$GLOBALS['db'] = new DatabaseHelper();