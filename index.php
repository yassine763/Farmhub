<?php 
require_once 'config/db.php'; 

// Si déjà connecté, on peut choisir de le laisser voir la landing ou le rediriger
// Ici on le laisse voir la landing s'il le souhaite, mais on met un bouton "Aller au flux"
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur FarmHub - Le réseau social agricole</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Header / Navigation -->
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">🌾 FarmHub</a>
                <ul class="nav-links">
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#how-it-works">Fonctionnement</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <div class="nav-btns">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="feed.php" class="btn btn-primary">Accéder au Flux <i class="fas fa-arrow-right"></i></a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Connexion</a>
                        <a href="register.php" class="btn btn-primary">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" style="min-height: 80vh; display: flex; align-items: center;">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <span style="background: var(--primary); color: white; padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; margin-bottom: 20px; display: inline-block;">PLATEFORME COMMUNAUTAIRE n°1</span>
                    <h1>FellahiHub : L'Agriculture de demain, ensemble.</h1>
                    <p>Partagez vos techniques, posez vos questions et apprenez des meilleurs experts et agriculteurs de votre région. Rejoignez une communauté solidaire.</p>
                    <div style="display: flex; gap: 15px; margin-top: 30px;">
                        <a href="register.php" class="btn btn-primary" style="padding: 15px 35px;">Rejoindre gratuitement</a>
                        <a href="#about" class="btn btn-outline" style="padding: 15px 35px;">En savoir plus</a>
                    </div>
                </div>
                <div class="hero-image-placeholder" style="background: url('https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&q=80&w=800'); background-size: cover; height: 500px; transform: rotate(0deg);">
                </div>
            </div>
        </div>
    </section>

    <!-- Info Section: Features -->
    <section id="about" class="section-padding" style="background: white;">
        <div class="container">
            <div style="text-align: center; max-width: 800px; margin: 0 auto 60px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Pourquoi rejoindre FarmHub ?</h2>
                <p style="color: var(--text-muted); font-size: 1.1rem;">Nous connectons les agriculteurs pour un partage de connaissances sans frontières.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <div style="text-align: center; padding: 30px; border-radius: 16px; background: var(--bg-cream);">
                    <div style="font-size: 3rem; margin-bottom: 20px; color: var(--primary);"><i class="fas fa-seedling"></i></div>
                    <h3>Partage d'Expérience</h3>
                    <p>Ne faites plus face à vos problèmes seul. Profitez du retour d'expérience de milliers de collègues.</p>
                </div>
                <div style="text-align: center; padding: 30px; border-radius: 16px; background: var(--bg-cream);">
                    <div style="font-size: 3rem; margin-bottom: 20px; color: var(--primary);"><i class="fas fa-handshake"></i></div>
                    <h3>Solidarité</h3>
                    <p>Une communauté prête à vous aider, que ce soit pour une maladie de culture ou une panne de matériel.</p>
                </div>
                <div style="text-align: center; padding: 30px; border-radius: 16px; background: var(--bg-cream);">
                    <div style="font-size: 3rem; margin-bottom: 20px; color: var(--primary);"><i class="fas fa-chart-line"></i></div>
                    <h3>Performance</h3>
                    <p>Optimisez vos rendements en adoptant les meilleures techniques partagées par nos experts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section id="how-it-works" class="section-padding" style="background: #f1f8e9;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem;">Comment ça marche ?</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 60px; text-align: center;">
                <div>
                    <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">1</div>
                    <h3>Créez votre compte</h3>
                    <p>Inscrivez-vous en quelques secondes avec votre email ou Google.</p>
                </div>
                <div>
                    <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">2</div>
                    <h3>Parcourez le flux</h3>
                    <p>Accédez aux dernières publications et conseils de la communauté.</p>
                </div>
                <div>
                    <div style="width: 50px; height: 50px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-weight: 700;">3</div>
                    <h3>Interagissez</h3>
                    <p>Aimez, commentez et partagez vos propres conseils avec des photos.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container" style="margin: 80px auto;">
        <div style="background: var(--primary); padding: 80px; border-radius: 30px; text-align: center; color: white;">
            <h2 style="color: white; font-size: 3rem; margin-bottom: 20px;">Prêt à cultiver le savoir ?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9;">Rejoignez-nous aujourd'hui et commencez à apprendre.</p>
            <a href="register.php" class="btn btn-white" style="padding: 15px 40px; font-size: 1.1rem; background: white; color: var(--primary);">Créer mon compte gratuitement</a>
        </div>
    </section>

    <footer style="padding: 60px 0; background: #1a2a1b; color: #888;">
        <div class="container" style="text-align: center;">
            <div class="logo" style="justify-content: center; margin-bottom: 30px; color: white;">🌾 FarmHub</div>
            <p>&copy; 2024 FarmHub. Tous droits réservés.</p>
        </div>
    </footer>

</body>
</html>
