<?php
session_start();
include 'connexion.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PétitionCitoyenne - Votre Voix Compte</title>
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
        }

        /* Navigation */
        nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
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

        .btn-primary {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white !important;
            padding: 0.7rem 1.5rem !important;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-secondary {
            background: transparent;
            background: #ffffffff;
            /* border: 2px solid #1e88e5; */
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

        .user-info {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 8rem 2rem 6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% {
                transform: rotate(0deg) translate(0, 0);
            }

            100% {
                transform: rotate(360deg) translate(-50px, -50px);
            }
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #1e88e5;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            font-weight: bold;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Stats Section */
        .stats {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            padding: 4rem 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* CTA Section */
        .cta {
            padding: 6rem 2rem;
            background-color: #f8f9fa;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #1e88e5;
        }

        .cta p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            padding: 3rem 2rem 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #1e88e5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-container {
                padding: 0 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .hero {
                padding: 6rem 1rem 4rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .section-title {
                font-size: 2rem;
            }

            .features,
            .cta {
                padding: 4rem 1rem;
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="ajouterPetition.php">Créer</a>
                    <a href="mesPetitions.php">Mes Pétitions</a>
                    <span class="user-info"><?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
                    <a href="logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="btn-secondary">Se connecter</a>
                    <a href="register.php" class="btn-primary">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Votre Voix Peut Changer le Monde</h1>
            <p>Rejoignez notre plateforme citoyenne et faites entendre votre voix. Créez, signez et soutenez les pétitions qui vous tiennent à cœur.</p>
            <div class="hero-buttons">
                <?php if (!isset($_SESSION['user_id'])):
                    echo '<a href="register.php" class="btn-primary">Commencer maintenant</a>'
                ?>
                <?php endif; ?>
                <a href="ListePetitions.php" class="btn-secondary">Voir les pétitions</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Pourquoi choisir PétitionCitoyenne ?</h2>
            <p class="section-subtitle">Une plateforme moderne et intuitive pour faire entendre votre voix</p>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">1</div>
                    <h3>Créer des pétitions</h3>
                    <p>Lancez vos propres pétitions en quelques clics et mobilisez la communauté autour de vos causes.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">2</div>
                    <h3>Signer facilement</h3>
                    <p>Soutenez les causes qui vous importent en signant des pétitions en quelques secondes.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">3</div>
                    <h3>Suivi en temps réel</h3>
                    <p>Visualisez l'impact de vos actions avec des statistiques détaillées et des mises à jour en direct.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">4</div>
                    <h3>Impact mondial</h3>
                    <p>Rejoignez une communauté mondiale de citoyens engagés pour le changement.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">5</div>
                    <h3>Sécurisé & Confidentiel</h3>
                    <p>Vos données sont protégées et vos signatures sont sécurisées avec les dernières technologies.</p>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">6</div>
                    <h3>Responsive Design</h3>
                    <p>Accédez à la plateforme depuis tous vos appareils, où que vous soyez.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Prêt à faire entendre votre voix ?</h2>
            <p>Rejoignez des milliers de citoyens qui utilisent notre plateforme pour créer le changement.</p>
            <?php if (!isset($_SESSION['user_id'])):
                echo '<a href="register.php" class="btn-primary" style="display: inline-block; margin-top: 1rem;">Rejoindre maintenant</a>'
            ?>
            <?php endif; ?>
            
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#features">Fonctionnalités</a>
                <a href="ListePetitions.php">Pétitions</a>
            </div>
            <p>&copy; 2024 PétitionCitoyenne. Tous droits réservés. | Créé avec passion pour la démocratie participative</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>

</html>