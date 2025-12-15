<?php
session_start();
include 'connexion.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Store errors from previous attempt if any
$errors = isset($_SESSION['login_errors']) ? $_SESSION['login_errors'] : [];
unset($_SESSION['login_errors']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se connecter - PétitionCitoyenne</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
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
            max-width: 400px;
            padding: 2rem;
            margin-top: 80px;
        }

        .login-card {
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
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.1);
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
                padding: 1rem;
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
                <a href="register.php" class="btn-secondary">S'inscrire</a>
            </div>
        </div>
    </nav>

    <!-- Login Container -->
    <div class="container">
        <div class="login-card">
            <div class="card-header">
                <h1>Connexion</h1>
                <p>Bienvenue sur PétitionCitoyenne</p>
            </div>

            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>Erreur(s) lors de la connexion :</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="login_process.php" method="POST">
                    <div class="form-group">
                        <label for="email">Adresse Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            placeholder="exemple@email.com"
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="Votre mot de passe"
                            autocomplete="current-password"
                        >
                    </div>

                    <button type="submit" class="submit-btn">Se connecter</button>
                </form>
            </div>

            <div class="card-footer">
                <p>Pas encore de compte ?</p>
                <a href="register.php">Créer un compte</a>
            </div>
        </div>
    </div>
</body>
</html>
