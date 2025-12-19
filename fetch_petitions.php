<?php
include 'connexion.php';

// Get all petitions sorted by date (newest first)
$query = "SELECT * FROM petition ORDER BY DateAjoutP DESC";
$stmt = oci_parse($conn, $query);

if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("Oracle error: " . $e['message']);
}

$petitions = [];
while ($row = oci_fetch_assoc($stmt)) {
    $petitions[] = $row;
}
oci_free_statement($stmt);

if (empty($petitions)) {
    echo "<p>Aucune pétition pour le moment.</p>";
} else {
    foreach ($petitions as $petition) {

        // ✅ HANDLE CLOB HERE (correct place)
        $desc = $petition['DESCRIPTIONP'];
        if ($desc instanceof OCILob) {
            $desc = $desc->load();
        }

        echo '<div class="petition-card">';
        echo '<div class="petition-title">'
            . htmlspecialchars($petition['TITREP'])
            . '</div>';

        echo '<div class="petition-meta">';
        echo '<span><strong>Description:</strong> '
            . htmlspecialchars(substr($desc, 0, 50))
            . (strlen($desc) > 50 ? '...' : '')
            . '</span>';

        echo '<span><strong>Date d\'ajout:</strong> '
            . htmlspecialchars($petition['DATEAJOUTP'])
            . '</span>';

        echo '<span><strong>Date de fin:</strong> '
            . htmlspecialchars($petition['DATEFINP'])
            . '</span>';

        echo '<span><strong>Porteur:</strong> '
            . htmlspecialchars($petition['NOMPORTEURP'])
            . '</span>';

        echo '</div>';

        echo '<div class="petition-actions">';
        echo '<a href="signature.php?idp='
            . $petition['IDP']
            . '" class="btn btn-primary">Signer</a>';
        echo '</div>';

        echo '</div>';
    }
}
