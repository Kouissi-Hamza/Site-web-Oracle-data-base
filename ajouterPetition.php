<?php
include 'connexion.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$defaultNom   = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
$defaultEmail = $_SESSION['user_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre       = $_POST['titre'];
    $description = $_POST['description'];
    $dateFin     = $_POST['datefin'];   // format YYYY-MM-DD from input[type=date]
    $nomPorteur  = $_POST['nomporteur'];
    $email       = $_POST['email'];

    // Oracle INSERT
    $query = "
        INSERT INTO petition
        (TitreP, DescriptionP, DateAjoutP, DateFinP, NomPorteurP, Email)
        VALUES
        (:titre, :description, SYSDATE, TO_DATE(:dateFin, 'YYYY-MM-DD'), :nomPorteur, :email)
    ";

    $stmt = oci_parse($conn, $query);

    oci_bind_by_name($stmt, ':titre', $titre);
    oci_bind_by_name($stmt, ':description', $description);
    oci_bind_by_name($stmt, ':dateFin', $dateFin);
    oci_bind_by_name($stmt, ':nomPorteur', $nomPorteur);
    oci_bind_by_name($stmt, ':email', $email);

    if (!oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
        $e = oci_error($stmt);
        die("Oracle error: " . $e['message']);
    }

    oci_free_statement($stmt);

    header("Location: ListePetitions.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une pétition</title>
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
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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

        .nav-links .active {
            background: #1e88e5;
            color: white;
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
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        h2 {
            text-align: center;
            color: #1e88e5;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
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
        input[type="email"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            outline: none;
            border-color: #1e88e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .submit-btn {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
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
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.3);
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
                <a href="ajouterPetition.php" class="active">Créer</a>
                <a href="mesPetitions.php">Mes Pétitions</a>
                <span class="user-info"><?php echo htmlspecialchars($_SESSION['user_prenom']); ?></span>
                <a href="logout.php">Déconnexion</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="form-card">
            <form method="POST" action="">
                <h2>Créer une nouvelle pétition</h2>

                <div class="form-group">
                    <label for="titre">Titre de la pétition</label>
                    <input type="text" id="titre" name="titre" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="datefin">Date de fin</label>
                    <input type="date" id="datefin" name="datefin" required>
                </div>

                <div class="form-group">
                    <label for="nomporteur">Nom du porteur</label>
                    <input type="text" id="nomporteur" name="nomporteur" value="<?php echo htmlspecialchars($defaultNom); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email du porteur</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($defaultEmail); ?>" required>
                </div>

                <button type="submit" class="submit-btn">Créer la pétition</button>
            </form>

            <a href="ListePetitions.php" class="back-link">Retour à la liste des pétitions</a>
        </div>
    </div>

</body>

</html>