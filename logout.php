<?php
/**
 * RxHub Logout API
 * Handles user and investor logout
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

// Destroy all session data
$_SESSION = [];

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Check if redirect is requested
if (isset($_GET['redirect'])) {
    $redirect = htmlspecialchars($_GET['redirect'], ENT_QUOTES, 'UTF-8');
    header("Location: $redirect");
    exit();
}

echo json_encode([
    'success' => true,
    'message' => 'Logged out successfully'
]);
?>
