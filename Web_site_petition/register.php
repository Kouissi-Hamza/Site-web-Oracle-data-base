<?php
session_start();
include 'connexion.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Store errors from previous attempt if any
$errors = isset($_SESSION['register_errors']) ? $_SESSION['register_errors'] : [];
unset($_SESSION['register_errors']);

// Store previous form data if any
$formData = isset($_SESSION['register_form_data']) ? $_SESSION['register_form_data'] : [];
unset($_SESSION['register_form_data']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S'inscrire - PétitionCitoyenne</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            min-height: 100vh;
            padding: 20px;
        }

        /* Navigation */
        nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            left: 0;
            right: 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo {
            color: #1e88e5;
            font-size: 1.8rem;
            font-weight: bold;
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
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-links a:hover {
            background: #1e88e5;
            color: white;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid #1e88e5;
            color: #1e88e5 !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #1e88e5;
            color: white !important;
            transform: translateY(-2px);
        }

        /* Main Container */
        .container {
            width: 100%;
            max-width: 500px;
            margin: 100px auto 40px;
        }

        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-header {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .card-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .card-header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Error Messages */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        .alert ul {
            margin-left: 1.5rem;
            margin-top: 0.5rem;
        }

        .alert li {
            margin-bottom: 0.3rem;
        }

        /* Form Groups */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
        }

        .form-group select {
            cursor: pointer;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 12px 15px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Links Section */
        .card-footer {
            padding: 1.5rem 2rem;
            background-color: #f8f9fa;
            text-align: center;
            border-top: 1px solid #e1e5e9;
        }

        .card-footer p {
            margin-bottom: 0.5rem;
            color: #666;
        }

        .card-footer a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .card-footer a:hover {
            color: #1565c0;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                margin-top: 80px;
            }

            .card-header {
                padding: 1.5rem;
            }

            .card-header h1 {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .card-footer {
                padding: 1rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Password requirements */
        .password-info {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">PétitionCitoyenne</a>
            <div class="nav-links">
                <a href="index.php">Accueil</a>
                <a href="ListePetitions.php">Pétitions</a>
                <a href="login.php" class="btn-secondary">Se connecter</a>
            </div>
        </div>
    </nav>

    <!-- Register Container -->
    <div class="container">
        <div class="register-card">
            <div class="card-header">
                <h1>Inscription</h1>
                <p>Rejoignez notre communauté</p>
            </div>

            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>Erreur(s) lors de l'inscription :</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="register_process.php" method="POST">
                    <!-- Nom and Prenom Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom</label>
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                required 
                                placeholder="Votre nom"
                                value="<?php echo htmlspecialchars($formData['nom'] ?? ''); ?>"
                            >
                        </div>

                        <div class="form-group">
                            <label for="prenom">Prénom</label>
                            <input 
                                type="text" 
                                id="prenom" 
                                name="prenom" 
                                required 
                                placeholder="Votre prénom"
                                value="<?php echo htmlspecialchars($formData['prenom'] ?? ''); ?>"
                            >
                        </div>
                    </div>

                    <!-- Pays -->
                    <div class="form-group">
                        <label for="pays">Pays</label>
                        <select id="pays" name="pays" required>
                            <option value="">Sélectionnez votre pays</option>
                            <option value="France" <?php echo (isset($formData['pays']) && $formData['pays'] === 'France') ? 'selected' : ''; ?>>France</option>
                            <option value="Belgique" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Belgique') ? 'selected' : ''; ?>>Belgique</option>
                            <option value="Suisse" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Suisse') ? 'selected' : ''; ?>>Suisse</option>
                            <option value="Canada" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Canada') ? 'selected' : ''; ?>>Canada</option>
                            <option value="Maroc" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Maroc') ? 'selected' : ''; ?>>Maroc</option>
                            <option value="Algérie" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Algérie') ? 'selected' : ''; ?>>Algérie</option>
                            <option value="Tunisie" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Tunisie') ? 'selected' : ''; ?>>Tunisie</option>
                            <option value="Autre" <?php echo (isset($formData['pays']) && $formData['pays'] === 'Autre') ? 'selected' : ''; ?>>Autre</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Adresse Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            placeholder="votre.email@exemple.com"
                            value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                            autocomplete="email"
                        >
                    </div>

                    <!-- Password and Confirm Password Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                minlength="6"
                                placeholder="Minimum 6 caractères"
                                autocomplete="new-password"
                            >
                            <div class="password-info">Minimum 6 caractères</div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirmer le mot de passe</label>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required 
                                minlength="6"
                                placeholder="Répétez le mot de passe"
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">S'inscrire</button>
                </form>
            </div>

            <div class="card-footer">
                <p>Vous avez déjà un compte ?</p>
                <a href="login.php">Se connecter</a>
            </div>
        </div>
    </div>
</body>
</html>
