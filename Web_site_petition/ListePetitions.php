<?php
include 'connexion.php';

if(!isLoggedIn()) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des pétitions</title>
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
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .top-stats {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        .top-stats strong {
            color: #1e88e5;
            font-size: 1.1rem;
        }

        .add-btn {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-left: 1rem;
        }

        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        h1 {
            text-align: center;
            color: white;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .petitions-grid {
            display: grid;
            gap: 1.5rem;
        }

        .petition-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .petition-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .petition-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #1e88e5;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .petition-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .petition-meta span {
            background: #f8f9fa;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            text-align: center;
        }

        .petition-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        .signature-count {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            text-align: center;
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

            h1 {
                font-size: 2rem;
            }

            .petition-meta {
                grid-template-columns: 1fr;
            }

            .petition-actions {
                flex-direction: column;
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
            <a href="ListePetitions.php" class="active">Pétitions</a>
            <a href="ajouterPetition.php">Créer</a>
            <a href="mesPetitions.php">Mes Pétitions</a>
            <span class="user-info"><?php echo htmlspecialchars($_SESSION['user_prenom']); ?></span>
            <a href="logout.php">Déconnexion</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="top-stats">
        <strong>Pétition la plus signée :</strong>
        <span id="topPetition">Chargement...</span>
        <a href="ajouterPetition.php" class="add-btn">+ Nouvelle pétition</a>
    </div>

    <h1>Liste des Pétitions</h1>

    <div id="petitionContainer" class="petitions-grid">
        <!-- Petitions via AJAX -->
    </div>
</div>

</body>
</html>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        function loadPetitions() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_petitions.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("petitionContainer").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        loadPetitions();

        setInterval(loadPetitions, 5000);


        function updateTop() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "plusSignee.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.TitreP !== undefined) {
                            document.getElementById('topPetition').textContent =
                                data.TitreP + ' : ' + data.nbr + ' signatures';
                        } else {
                            document.getElementById('topPetition').textContent = 'Aucune pétition';
                        }
                    } catch(e) {
                        console.error("Erreur JSON", e);
                    }
                }
            };
            xhr.send();
        }

        updateTop();
        setInterval(updateTop, 2000);


        let lastId = 0;
        let initialised = false;

        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }

        function showNotification(message) {
            if (Notification.permission === "granted") {
                new Notification(message);
            }
        }

        function verifierNouveauRecord() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "notifNouvellepetition.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        if (data.lastId !== undefined) {
                            if (initialised && data.lastId > lastId) {
                                showNotification("Nouvelle pétition ajoutée !");
                                loadPetitions();
                                updateTop();
                            }
                            lastId = data.lastId;
                            initialised = true;
                        }
                    } catch(e) {
                        console.error("Erreur JSON", e);
                    }
                }
            };
            xhr.send();
        }

        verifierNouveauRecord();
        setInterval(verifierNouveauRecord, 2000);
    });
</script>
