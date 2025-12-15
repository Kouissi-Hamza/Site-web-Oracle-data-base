<?php
include 'connexion.php';
header('Content-Type: application/json; charset=utf-8');

$stmt = $conn->query("SELECT MAX(IDP) AS lastId FROM petition");
$lastId = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($lastId ?: ['lastId' => 0]);
?>
