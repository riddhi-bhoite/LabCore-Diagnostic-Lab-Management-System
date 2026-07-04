<?php
// ============================================================
//  api/login.php — Login API Endpoint
//  Method : POST
//  Body   : { "email": "...", "password": "..." }
//  Returns: { success, message, role, redirect, name }
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.');
}

$body     = getBody();
$email    = clean($body['email']    ?? '');
$password = $body['password'] ?? '';   // don't strip — bcrypt needs raw

if (!$email || !$password) {
    respond(false, 'Email and password are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Invalid email format.');
}

$db   = getDB();
$stmt = $db->prepare("SELECT Person_ID, First_Name, Last_Name, Role, Password_Hash FROM Person WHERE Email_ID = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['Password_Hash'])) {
    respond(false, 'Invalid email or password.');
}

// Set session
$_SESSION['person_id']  = $user['Person_ID'];
$_SESSION['first_name'] = $user['First_Name'];
$_SESSION['last_name']  = $user['Last_Name'];
$_SESSION['role']       = $user['Role'];

respond(true, 'Login successful.', [
    'role'     => $user['Role'],
    'name'     => $user['First_Name'] . ' ' . $user['Last_Name'],
    'redirect' => dashboardUrl($user['Role']),
]);