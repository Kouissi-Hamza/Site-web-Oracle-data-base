<?php
include 'connexion.php';
// session_start();

// // Ensure user is logged in
// if (!isset($_SESSION['user_email'])) {
//     exit('Unauthorized');
// }

$userEmail = $_SESSION['user_email'];

// Get user's petitions
$query = "SELECT * FROM PETITION WHERE EMAIL = :email ORDER BY DATEAJOUTP DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':email', $userEmail);

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
    echo '<div class="empty-state">
            <p>Vous n\'avez pas encore créé de pétition.</p>
            <a href="ajouterPetition.php">Créer ma première pétition</a>
          </div>';
} else {

    echo '<div class="petitions-grid">';

    foreach ($petitions as $petition) {

        /* -------- Handle CLOB DESCRIPTIONP -------- */
        $desc = $petition['DESCRIPTIONP'];
        if ($desc instanceof OCILob) {
            $desc = $desc->load();
        }

        /* -------- Count signatures -------- */
        $queryCount = "SELECT COUNT(*) AS CNT FROM SIGNATURE WHERE IDP = :idp";
        $stmtCount = oci_parse($conn, $queryCount);
        oci_bind_by_name($stmtCount, ':idp', $petition['IDP']);
        oci_execute($stmtCount);
        $countRow = oci_fetch_assoc($stmtCount);
        $signatureCount = $countRow['CNT'] ?? 0;
        oci_free_statement($stmtCount);

        echo '<div class="petition-card">
                <div class="petition-title">' . htmlspecialchars($petition['TITREP']) . '</div>

                <div class="petition-meta">
                    <span><strong>Description:</strong> '
            . htmlspecialchars(substr($desc, 0, 50))
            . (strlen($desc) > 50 ? '...' : '') .
            '</span>

                    <span><strong>Date d\'ajout:</strong> '
            . htmlspecialchars($petition['DATEAJOUTP']) .
            '</span>

                    <span><strong>Date de fin:</strong> '
            . htmlspecialchars($petition['DATEFINP']) .
            '</span>

                    <span><strong>Signatures:</strong> '
            . $signatureCount .
            '</span>
                </div>

                <div class="petition-actions">
                    <a href="modifierPetition.php?id=' . $petition['IDP'] . '" class="btn btn-edit">Modifier</a>
                    <a href="supprimerPetition.php?id=' . $petition['IDP'] . '"
                       class="btn btn-delete"
                       onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette pétition ?\')">
                       Supprimer
                    </a>
                </div>
              </div>';
    }

    echo '</div>';
}
