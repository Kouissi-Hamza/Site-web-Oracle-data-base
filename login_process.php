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
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':email', $email);
            if (!oci_execute($stmt)) {
                $errors[] = "Error retrieving user: " . oci_error_message($stmt);
            } else {
                $user = oci_fetch_assoc($stmt);

                // Check if user exists and password is correct
                if ($user && password_verify($password, $user['PASSWORD'])) {
                    // Set session variables (OCI returns uppercase column names)
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['user_nom'] = $user['NOM'];
                    $_SESSION['user_prenom'] = $user['PRENOM'];
                    $_SESSION['user_email'] = $user['EMAIL'];
                    $_SESSION['user_pays'] = $user['PAYS'];

                    header("Location: ListePetitions.php");
                    exit();
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            }
            oci_free_statement($stmt);
        } catch (Exception $e) {
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
