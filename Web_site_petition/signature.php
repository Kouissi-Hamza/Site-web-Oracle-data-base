<?php
include 'connexion.php';

// Require login to sign petitions
if(!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Pre-fill data from logged in user
$defaultNom = $_SESSION['user_nom'];
$defaultPrenom = $_SESSION['user_prenom'];
$defaultEmail = $_SESSION['user_email'];
$defaultPays = $_SESSION['user_pays'];

if (isset($_GET['idp'])) {
    $idp = $_GET['idp'];

    $stmt = $conn->prepare("SELECT * FROM petition WHERE IDP = ?");
    $stmt->execute([$idp]);
    $petition = $stmt->fetch();
} else {
    die("Aucune pétition sélectionnée !");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Signer la pétition</title>
    <style>
        /* Modern beautiful design */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #333;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1e88e5;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: #1e88e5;
            color: white;
            transform: translateY(-2px);
        }

        .user-info {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }

        .container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }

        h2 {
            text-align: center;
            color: #1e88e5;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .petition-info {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            border: 1px solid #e1e8ed;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .petition-info h3 {
            color: #1e88e5;
            margin: 0 0 1rem 0;
            font-size: 1.2rem;
        }

        .recent-signatures {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border: 1px solid #ffeaa7;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .recent-signatures h3 {
            color: #856404;
            margin: 0 0 1rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .signatures-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            max-height: 300px;
            overflow-y: auto;
        }

        .signature-item {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            padding: 1rem;
            text-align: left;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }

        .signature-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .signature-name {
            font-weight: bold;
            color: #1e88e5;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .signature-details {
            font-size: 0.85rem;
            color: #666;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .signature-detail {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .signature-detail strong {
            color: #555;
        }

        .loading {
            color: #856404;
            font-style: italic;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
        }

        .no-signatures {
            color: #856404;
            font-style: italic;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            text-align: center;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar styling for signatures list */
        .signatures-list::-webkit-scrollbar {
            width: 6px;
        }

        .signatures-list::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }

        .signatures-list::-webkit-scrollbar-thumb {
            background: rgba(133, 100, 4, 0.5);
            border-radius: 3px;
        }

        .signatures-list::-webkit-scrollbar-thumb:hover {
            background: rgba(133, 100, 4, 0.7);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .submit-btn {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            color: #1e88e5;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-content {
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .form-card {
                padding: 1.5rem;
                margin: 1rem;
            }

            .recent-signatures {
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .recent-signatures h3 {
                font-size: 1rem;
            }

            .signature-item {
                padding: 0.75rem;
            }

            .signature-name {
                font-size: 0.9rem;
            }

            .signature-details {
                font-size: 0.8rem;
                gap: 0.5rem;
            }

            .signature-detail {
                flex: 1 1 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <div class="header-content">
        <a href="index.php" class="logo">Gestion des Pétitions</a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="ListePetitions.php">Pétitions</a>
            <a href="ajouterPetition.php">Créer</a>
            <a href="mesPetitions.php">Mes Pétitions</a>
            <span class="user-info"><?php echo htmlspecialchars($_SESSION['user_prenom']); ?></span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="form-card">
        <form action="ajouterSignature.php" method="POST">
            <div class="recent-signatures">
                <h3>Dernières signatures</h3>
                <div id="top5Petition" class="signatures-list">
                    <div class="loading">Chargement des signatures...</div>
                </div>
            </div>
<!--            <div class="petition-info">-->
<!--                <p><strong>Titre :</strong> --><?php //= htmlspecialchars($petition['TitreP']); ?><!--</p>-->
<!--                <p><strong>Description :</strong> --><?php //= htmlspecialchars($petition['DescriptionP']); ?><!--</p>-->
<!--            </div>-->
            <h2>Signer la pétition</h2>

            <div class="petition-info">
                <h3><?php echo htmlspecialchars($petition['TitreP']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($petition['DescriptionP']); ?></p>
            </div>

            <input type="hidden" name="idp" value="<?php echo $petition['IDP']; ?>">

            <div class="form-group">
                <label for="nomS">Nom</label>
                <input type="text" name="nomS" value="<?php echo htmlspecialchars($defaultNom); ?>" required>
            </div>

            <div class="form-group">
                <label for="prenomS">Prénom</label>
                <input type="text" name="prenomS" value="<?php echo htmlspecialchars($defaultPrenom); ?>" required>
            </div>

            <div class="form-group">
                <label for="paysS">Pays</label>
                <input type="text" name="paysS" value="<?php echo htmlspecialchars($defaultPays); ?>" required>
            </div>

            <div class="form-group">
                <label for="emailS">Email</label>
                <input type="email" name="emailS" value="<?php echo htmlspecialchars($defaultEmail); ?>" required>
            </div>

            <button type="submit" class="submit-btn">Signer la pétition</button>
        </form>

        <a href="ListePetitions.php" class="back-link">Retour à la liste des pétitions</a>
    </div>
</div>

</body>
</html>
<script>

    function RecupText() {
        objetXHR = new XMLHttpRequest();
        objetXHR.open("get","dernieres5ajoutee.php?idp=<?= $idp ?>", true);
        objetXHR.onreadystatechange = function() {
            if (objetXHR.readyState === 4 && objetXHR.status === 200) {
                let data = JSON.parse(objetXHR.responseText);
                let html = "";

                if (data.length === 0) {
                    html = '<div class="no-signatures">Aucune signature pour cette pétition</div>';
                } else {
                    data.forEach((item, index) => {
                        const date = new Date(item.DateS);
                        const formattedDate = date.toLocaleDateString('fr-FR');
                        const formattedTime = item.Heures ? item.Heures.substring(0, 5) : '';

                        html += `
                            <div class="signature-item">
                                <div class="signature-name">${item.PrenomS} ${item.NomS}</div>
                                <div class="signature-details">
                                    <span class="signature-detail">
                                        <strong></strong> ${item.EmailS}
                                    </span>
                                    <span class="signature-detail">
                                        <strong></strong> ${item.PaysS}
                                    </span>
                                    <span class="signature-detail">
                                        <strong></strong> ${formattedDate} ${formattedTime}
                                    </span>
                                </div>
                            </div>
                        `;
                    });
                }

                document.getElementById("top5Petition").innerHTML = html;
            }
        };
        objetXHR.send();
    }
    RecupText();
    setInterval(RecupText,5000);
</script>