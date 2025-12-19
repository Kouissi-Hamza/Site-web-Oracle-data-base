<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idp      = $_POST['idp'];
    $nomS     = $_POST['nomS'];
    $prenomS  = $_POST['prenomS'];
    $paysS    = $_POST['paysS'];
    $emailS   = $_POST['emailS'];

    $query = "
        INSERT INTO SIGNATURE
        (IDP, NOMS, PRENOMS, PAYSS, DATES, HEURES, EMAILS)
        VALUES
        (:idp, :nomS, :prenomS, :paysS, SYSDATE, TO_CHAR(SYSDATE, 'HH24:MI:SS'), :emailS)
    ";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':idp', $idp);
    oci_bind_by_name($stmt, ':nomS', $nomS);
    oci_bind_by_name($stmt, ':prenomS', $prenomS);
    oci_bind_by_name($stmt, ':paysS', $paysS);
    oci_bind_by_name($stmt, ':emailS', $emailS);

    if (!oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
        $e = oci_error($stmt);
        die("Oracle error: " . $e['message']);
    }

    oci_free_statement($stmt);

    header("Location: ListePetitions.php");
    exit();
}
