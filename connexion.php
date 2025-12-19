<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Oracle database connection
$conn = oci_connect(
    "petitionUser",           // Oracle username
    "app123",            // Oracle password
    "localhost/XEPDB1"   // Oracle service name
);

if (!$conn) {
    $e = oci_error();
    die("Oracle connection failed: " . $e['message']);
}

// Helper function for OCI error handling
function oci_error_message($resource = null)
{
    $error = oci_error($resource);
    return $error ? $error['message'] : 'Unknown error';
}

// Simple helper function
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function oci_clob_to_string($value)
{
    if ($value instanceof OCILob) {
        return $value->load();
    }
    return $value;
}
