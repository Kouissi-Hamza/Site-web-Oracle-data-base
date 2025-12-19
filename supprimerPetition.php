<?php
include 'connexion.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Get petition ID
if (!isset($_GET['id'])) {
    header("Location: mesPetitions.php");
    exit();
}

$petitionId = $_GET['id'];
$userEmail = $_SESSION['user_email'];

// Verify ownership
$query = "SELECT * FROM petition WHERE IDP = :idp AND Email = :email";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':idp', $petitionId);
oci_bind_by_name($stmt, ':email', $userEmail);
if (!oci_execute($stmt)) {
    die("Error executing query: " . oci_error_message($stmt));
}
$petition = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$petition) {
    header("Location: mesPetitions.php");
    exit();
}

// Delete signatures first (foreign key constraint)
$query = "DELETE FROM signature WHERE IDP = :idp";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':idp', $petitionId);
if (!oci_execute($stmt)) {
    die("Error executing query: " . oci_error_message($stmt));
}
oci_free_statement($stmt);

// Delete petition
$query = "DELETE FROM petition WHERE IDP = :idp";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':idp', $petitionId);
if (!oci_execute($stmt)) {
    die("Error executing query: " . oci_error_message($stmt));
}
oci_free_statement($stmt);

// Redirect back
header("Location: mesPetitions.php");
exit();
