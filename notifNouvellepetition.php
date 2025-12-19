<?php
include 'connexion.php';
header('Content-Type: application/json; charset=utf-8');

$query = "SELECT MAX(IDP) AS LASTID FROM petition";
$stmt = oci_parse($conn, $query);
if (!oci_execute($stmt)) {
    die("Error executing query: " . oci_error_message($stmt));
}
$row = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

$lastId = $row && isset($row['LASTID']) ? intval($row['LASTID']) : 0;
echo json_encode(['lastId' => $lastId]);
