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
    $query = "SELECT id FROM users WHERE email = :email";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ':email', $email);
    if (!oci_execute($stmt)) {
        $errors[] = "Error checking email: " . oci_error_message($stmt);
    } else {
        $user = oci_fetch_assoc($stmt);
        if ($user) {
            $errors[] = "Cette adresse email est déjà utilisée.";
        }
    }
    oci_free_statement($stmt);

    // If no errors, proceed with registration
    if (empty($errors)) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database and return generated id
            $newId = 0;
            $query = "INSERT INTO users (nom, prenom, pays, email, password)
                      VALUES (:nom, :prenom, :pays, :email, :password)
                      RETURNING id INTO :new_id";
            $stmt = oci_parse($conn, $query);
            oci_bind_by_name($stmt, ':nom', $nom);
            oci_bind_by_name($stmt, ':prenom', $prenom);
            oci_bind_by_name($stmt, ':pays', $pays);
            oci_bind_by_name($stmt, ':email', $email);
            oci_bind_by_name($stmt, ':password', $hashedPassword);
            oci_bind_by_name($stmt, ':new_id', $newId, 32);
            if (!oci_execute($stmt, OCI_COMMIT_ON_SUCCESS)) {
                $errors[] = "Erreur lors de l'inscription : " . oci_error_message($stmt);
            } else {
                $_SESSION['user_id'] = $newId;
                $_SESSION['user_nom'] = $nom;
                $_SESSION['user_prenom'] = $prenom;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_pays'] = $pays;

                header("Location: index.php");
                exit();
            }
            oci_free_statement($stmt);
        } catch (Exception $e) {
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
