<?php
include 'connexion.php';

// Get all petitions sorted by date (newest first)
$stmt = $conn->prepare("SELECT * FROM petition ORDER BY DateAjoutP DESC");
$stmt->execute();
$petitions = $stmt->fetchAll();

if (empty($petitions)) {
    echo "<p>Aucune pétition pour le moment.</p>";
} else {
    foreach ($petitions as $petition) {
        echo '<div class="petition-card">';
        echo '<div class="petition-title">' . htmlspecialchars($petition['TitreP']) . '</div>';
        echo '<div class="petition-meta">';
        echo '<span><strong>Description:</strong> ' . htmlspecialchars(substr($petition['DescriptionP'], 0, 50)) . (strlen($petition['DescriptionP']) > 50 ? "..." : "") . '</span>';
        echo '<span><strong>Date d\'ajout:</strong> ' . htmlspecialchars($petition['DateAjoutP']) . '</span>';
        echo '<span><strong>Date de fin:</strong> ' . htmlspecialchars($petition['DateFinP']) . '</span>';
        echo '<span><strong>Porteur:</strong> ' . htmlspecialchars($petition['NomPorteurP']) . '</span>';
        echo '</div>';
        echo '<div class="petition-actions">';
        echo '<a href="signature.php?idp=' . $petition['IDP'] . '" class="btn btn-primary">Signer</a>';
        echo '</div>';
        echo '</div>';
    }
}
?>
