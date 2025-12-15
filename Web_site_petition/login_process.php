<?php
session_start();
include 'connexion.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validation
    $errors = [];

    // Check required fields
    if (empty($email) || empty($password)) {
        $errors[] = "Email et mot de passe sont obligatoires.";
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }

    // If no validation errors, proceed with login
    if (empty($errors)) {
        try {
            // Get user from database
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_pays'] = $user['pays'];

                header("Location: ListePetitions.php");
                exit();

            } else {
                $errors[] = "Email ou mot de passe incorrect.";
            }

        } catch(PDOException $e) {
            $errors[] = "Erreur lors de la connexion : " . $e->getMessage();
        }
    }

    // If there are errors, store them in session and redirect back
    if (!empty($errors)) {
        $_SESSION['login_errors'] = $errors;
        header("Location: login.php");
        exit();
    }
} else {
    // If not POST request, redirect to home
    header("Location: login.php");
    exit();
}
?>
