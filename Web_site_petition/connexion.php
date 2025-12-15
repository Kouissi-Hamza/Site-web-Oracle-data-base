<?php
// Check if session is already started before calling session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new PDO('mysql:host=localhost;dbname=TP3', 'root', '');

// Simple helper function
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>