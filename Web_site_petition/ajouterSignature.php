<?php
include 'connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idp = $_POST['idp'];
    $nomS = $_POST['nomS'];
    $prenomS = $_POST['prenomS'];
    $paysS = $_POST['paysS'];
    $emailS = $_POST['emailS'];

    $dateS = date('Y-m-d');
    $heureS = date('H:i:s');

    $stmt = $conn->prepare("INSERT INTO signature (IDP, NomS, PrenomS, PaysS, DateS, HeureS, EmailS)
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idp, $nomS, $prenomS, $paysS, $dateS, $heureS, $emailS]);

    header("Location: ListePetitions.php");
    exit();
}
?>
