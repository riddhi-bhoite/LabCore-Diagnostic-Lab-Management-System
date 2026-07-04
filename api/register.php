<?php
// ============================================================
//  api/register.php — Patient Registration API Endpoint
//  Method : POST
//  Body   : { first_name, last_name, dob, gender, phone,
//             email, password, blood_group, address }
//  Returns: { success, message }
// ============================================================

require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Method not allowed.');
}

$body = getBody();

// ── Validate required fields ──────────────────────────────────
$required = ['first_name','last_name','dob','gender','phone','email','password'];
foreach ($required as $field) {
    if (empty(trim($body[$field] ?? ''))) {
        respond(false, "Field '$field' is required.");
    }
}

$firstName  = clean($body['first_name']);
$lastName   = clean($body['last_name']);
$dob        = clean($body['dob']);
$gender     = clean($body['gender']);
$phone      = clean($body['phone']);
$email      = clean($body['email']);
$password   = $body['password'];
$bloodGroup = clean($body['blood_group'] ?? '');
$address    = clean($body['address']     ?? '');

// ── Extra validations ─────────────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Invalid email address.');
}
if (strlen($password) < 8) {
    respond(false, 'Password must be at least 8 characters.');
}
if (!in_array($gender, ['Male','Female','Other'])) {
    respond(false, 'Invalid gender value.');
}

$db = getDB();

// ── Check duplicates ──────────────────────────────────────────
$stmt = $db->prepare("SELECT Person_ID FROM Person WHERE Email_ID = ? OR Phone_No = ?");
$stmt->execute([$email, $phone]);
if ($stmt->fetch()) {
    respond(false, 'Email or phone number is already registered.');
}

// ── Insert ────────────────────────────────────────────────────
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $db->beginTransaction();

    $stmt = $db->prepare("
        INSERT INTO Person (First_Name, Last_Name, DOB, Gender, Phone_No, Email_ID, Role, Password_Hash)
        VALUES (?, ?, ?, ?, ?, ?, 'Patient', ?)
    ");
    $stmt->execute([$firstName, $lastName, $dob, $gender, $phone, $email, $hash]);
    $personId = $db->lastInsertId();

    $stmt = $db->prepare("INSERT INTO Patient (Person_ID, Blood_Group, Address) VALUES (?, ?, ?)");
    $stmt->execute([$personId, $bloodGroup ?: null, $address ?: null]);

    $db->commit();
    respond(true, 'Registration successful! You can now log in.');

} catch (PDOException $e) {
    $db->rollBack();
    respond(false, 'Registration failed. Please try again.');
}