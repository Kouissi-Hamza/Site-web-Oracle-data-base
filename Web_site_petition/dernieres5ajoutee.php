<?php
include 'connexion.php';
header('Content-Type: application/json; charset=utf-8');

// Get petition ID from GET parameter
$idp = isset($_GET['idp']) ? $_GET['idp'] : '';

if (empty($idp)) {
    echo json_encode([]);
    exit();
}

$cinq = $conn->prepare("SELECT * FROM signature WHERE IDP = ?
                                ORDER BY DateS DESC, Heures DESC LIMIT 5;");
$cinq->execute([$idp]);
$cinq=$cinq->fetchAll();
if (!$cinq){
    echo json_encode([]);
}else {
    echo json_encode($cinq);
}
?>
