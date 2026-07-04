<?php
// ============================================================
//  includes/helpers.php — Shared API Utilities
// ============================================================

// Allow JSON requests from same origin
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');          // tighten in production
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

if (session_status() === PHP_SESSION_NONE) session_start();

// ── Send JSON response and exit ───────────────────────────────
function respond(bool $success, string $message, array $data = []): void {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
    exit;
}

// ── Get JSON body from fetch() POST ──────────────────────────
function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

// ── Sanitize a string ─────────────────────────────────────────
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)));
}

// ── Dashboard URL by role ─────────────────────────────────────
function dashboardUrl(string $role): string {
    return match ($role) {
        'Admin'          => '../pages/admin-dashboard.html',
        'Doctor'         => '../pages/doctor-dashboard.html',
        'Lab_Technician' => '../pages/lab-dashboard.html',
        'Patient'        => '../pages/patient-dashboard.html',
        default          => '../pages/login.html',
    };
}