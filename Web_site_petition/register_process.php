<?php
session_start();
include 'connexion.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $pays = trim($_POST['pays']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    $errors = [];

    // Check required fields
    if (empty($nom) || empty($prenom) || empty($pays) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }

    // Check password length
    if (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Check password confirmation
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->rowCount() > 0) {
        $errors[] = "Cette adresse email est déjà utilisée.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $conn->prepare("INSERT INTO users (nom, prenom, pays, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $pays, $email, $hashedPassword]);

            // Get the inserted user ID
            $userId = $conn->lastInsertId();

            // Set session variables
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_nom'] = $nom;
            $_SESSION['user_prenom'] = $prenom;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_pays'] = $pays;

            // Redirect to home page
            header("Location: index.php");
            exit();

        } catch(PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }

    // If there are errors, store them in session and redirect back
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        // Store form data to repopulate form
        $_SESSION['register_form_data'] = [
            'nom' => $nom,
            'prenom' => $prenom,
            'pays' => $pays,
            'email' => $email
        ];
        header("Location: register.php");
        exit();
    }
} else {
    // If not POST request, redirect to home
    header("Location: register.php");
    exit();
}
?>
