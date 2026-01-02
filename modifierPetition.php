<?php
include 'connexion.php';
// session_start();
 
/* ---------- Security checks ---------- */
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: mesPetitions.php");
    exit();
}

$petitionId = $_GET['id'];
$userEmail  = $_SESSION['user_email'];

/* ---------- Fetch petition ---------- */
$query = "SELECT * FROM PETITION WHERE IDP = :idp AND EMAIL = :email";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ':idp', $petitionId);
oci_bind_by_name($stmt, ':email', $userEmail);
oci_execute($stmt);

$petition = oci_fetch_assoc($stmt);
oci_free_statement($stmt);

if (!$petition) {
    header("Location: mesPetitions.php");
    exit();
}

/* ---------- Handle CLOB ---------- */
$descriptionText = oci_clob_to_string($petition['DESCRIPTIONP']);

/* ---------- Update petition ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre       = $_POST['titre'];
    $description = $_POST['description'];
    $dateFin     = $_POST['datefin']; // YYYY-MM-DD from HTML input

    $query = "
        UPDATE PETITION
        SET TITREP = :titre,
            DESCRIPTIONP = :description,
            DATEFINP = TO_DATE(:datefin, 'YYYY-MM-DD')
        WHERE IDP = :idp
    ";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':titre', $titre);
    oci_bind_by_name($stmt, ':description', $description);
    oci_bind_by_name($stmt, ':datefin', $dateFin);
    oci_bind_by_name($stmt, ':idp', $petitionId);

    oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
    oci_free_statement($stmt);

    header("Location: mesPetitions.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier la pétition</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
            margin: 3rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .15);
        }

        h2 {
            text-align: center;
            color: #1e88e5;
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: 500;
            display: block;
            margin-bottom: .4rem;
        }

        input,
        textarea {
            width: 100%;
            padding: .8rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 1.2rem;
        }

        textarea {
            min-height: 120px;
        }

        button {
            width: 100%;
            padding: .9rem;
            border-radius: 25px;
            border: none;
            background: linear-gradient(135deg, #17a2b8, #20c997);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            opacity: .9;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 1rem;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Modifier la pétition</h2>

        <form method="POST">
            <label>Titre</label>
            <input type="text" name="titre"
                value="<?php echo htmlspecialchars($petition['TITREP']); ?>" required>

            <label>Description</label>
            <textarea name="description" required><?php
                                                    echo htmlspecialchars($descriptionText);
                                                    ?></textarea>

            <label>Date de fin</label>
            <input type="date" name="datefin"
                value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($petition['DATEFINP']))); ?>"
                required>

            <button type="submit">Enregistrer</button>
        </form>

        <a href="mesPetitions.php" class="back">← Retour</a>
    </div>

</body>

</html>