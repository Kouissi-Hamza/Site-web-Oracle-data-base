<?php
include 'connexion.php';

// Check if user is logged in
if(!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Get petition ID
if(!isset($_GET['id'])) {
    header("Location: mesPetitions.php");
    exit();
}

$petitionId = $_GET['id'];
$userEmail = $_SESSION['user_email'];

// Verify ownership
$stmt = $conn->prepare("SELECT * FROM petition WHERE IDP = ? AND Email = ?");
$stmt->execute([$petitionId, $userEmail]);
$petition = $stmt->fetch();

if(!$petition) {
    header("Location: mesPetitions.php");
    exit();
}

// Delete signatures first (foreign key constraint)
$stmt = $conn->prepare("DELETE FROM signature WHERE IDP = ?");
$stmt->execute([$petitionId]);

// Delete petition
$stmt = $conn->prepare("DELETE FROM petition WHERE IDP = ?");
$stmt->execute([$petitionId]);

// Redirect back
header("Location: mesPetitions.php");
exit();
?>
