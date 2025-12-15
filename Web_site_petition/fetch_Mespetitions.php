<?php
include 'connexion.php';
//session_start();
//
//// Ensure user is logged in
//if (!isset($_SESSION['user_email'])) {
//    exit('Unauthorized');
//}

$userEmail = $_SESSION['user_email'];

// Get user's petitions
$stmt = $conn->prepare("SELECT * FROM petition WHERE Email = ? ORDER BY DateAjoutP DESC");
$stmt->execute([$userEmail]);
$petitions = $stmt->fetchAll();

if (empty($petitions)) {
    echo '<div class="empty-state">
            <p>Vous n\'avez pas encore créé de pétition.</p>
            <a href="ajouterPetition.php">Créer ma première pétition</a>
          </div>';
} else {
    echo '<div class="petitions-grid">';
    foreach ($petitions as $petition) {
        $sigCount = $conn->prepare("SELECT COUNT(*) as count FROM signature WHERE IDP = ?");
        $sigCount->execute([$petition['IDP']]);
        $signatureCount = $sigCount->fetch()['count'];

        echo '<div class="petition-card">
                <div class="petition-title">' . htmlspecialchars($petition['TitreP']) . '</div>
                <div class="petition-meta">
                    <span><strong>Description:</strong> ' . htmlspecialchars(substr($petition['DescriptionP'], 0, 50)) . (strlen($petition['DescriptionP']) > 50 ? '...' : '') . '</span>
                    <span><strong>Date d\'ajout:</strong> ' . htmlspecialchars($petition['DateAjoutP']) . '</span>
                    <span><strong>Date de fin:</strong> ' . htmlspecialchars($petition['DateFinP']) . '</span>
                    <span><strong>Signatures:</strong> ' . $signatureCount . '</span>
                </div>
                <div class="petition-actions">
                    <a href="modifierPetition.php?id=' . $petition['IDP'] . '" class="btn btn-edit">Modifier</a>
                    <a href="supprimerPetition.php?id=' . $petition['IDP'] . '" 
                       class="btn btn-delete" 
                       onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette pétition ?\')">Supprimer</a>
                </div>
              </div>';
    }
    echo '</div>';
}
?>
