<?php
// ============================================================
//  api/logout.php — Logout Endpoint
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';

session_unset();
session_destroy();
respond(true, 'Logged out.', ['redirect' => '../pages/login.html']);