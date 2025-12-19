<?php
include 'connexion.php';

$query = "
    SELECT
        s.IDP,
        p.TITREP,
        COUNT(*) AS NBR
    FROM PETITION p
    JOIN SIGNATURE s ON p.IDP = s.IDP
    GROUP BY s.IDP, p.TITREP
    ORDER BY NBR DESC
    FETCH FIRST 1 ROWS ONLY
";

$stmt = oci_parse($conn, $query);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("Oracle error: " . $e['message']);
}

$top = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

header('Content-Type: application/json');

if (!$top) {
    echo json_encode([]);
} else {
    echo json_encode($top);
}
