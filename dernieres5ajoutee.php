<?php
include 'connexion.php';
header('Content-Type: application/json; charset=utf-8');

// Get petition ID from GET parameter
$idp = isset($_GET['idp']) ? $_GET['idp'] : '';

if (empty($idp)) {
    echo json_encode([]);
    exit();
}

// Select with TO_CHAR for predictable date format and alias Heures to match frontend expectation
$query = "SELECT NomS, PrenomS, PaysS, EmailS,
                 TO_CHAR(DateS,'YYYY-MM-DD') AS DateS,
                 HeureS AS Heures
          FROM signature
          WHERE IDP = :idp
          ORDER BY DateS DESC, Heures DESC
          FETCH FIRST 5 ROWS ONLY";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':idp', $idp);
if (!oci_execute($stmt)) {
    die("Error executing query: " . oci_error_message($stmt));
}
$cinq = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cinq[] = [
        'NomS' => $row['NOMS'] ?? null,
        'PrenomS' => $row['PRENOMS'] ?? null,
        'PaysS' => $row['PAYSS'] ?? null,
        'EmailS' => $row['EMAILS'] ?? null,
        'DateS' => $row['DATES'] ?? null,
        'Heures' => $row['HEURES'] ?? null
    ];
}
oci_free_statement($stmt);

echo json_encode($cinq);
