<?php
// dashboard.php
require_once 'config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Utilisateur';
$avatar = $_SESSION['avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($username).'&background=2e7d32&color=fff';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - FarmHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: var(--bg-cream);">

    <!-- Simple Nav for Dashboard -->
    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">🌾 FarmHub</a>
                <div class="nav-btns">
                    <a href="logout.php" class="btn btn-outline" style="padding: 10px 24px;">Déconnexion</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="container" style="margin-top: 50px;">
        <div class="auth-card" style="max-width: 600px; margin: auto;">
            <div style="position: relative; display: inline-block;">
                <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 5px solid var(--white); box-shadow: var(--shadow);">
                <div style="position: absolute; bottom: 5px; right: 5px; background: #4caf50; width: 20px; height: 20px; border-radius: 50%; border: 3px solid #fff;"></div>
            </div>
            
            <h1 style="margin-top: 20px; font-size: 2.2rem; color: var(--primary);">Bienvenue, <?php echo htmlspecialchars($username); ?> !</h1>
            <p class="status" style="background: #e8f5e9; color: #2e7d32; padding: 8px 20px; border-radius: 50px; font-weight: 600; display: inline-block; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> Compte vérifié
            </p>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 30px;">
                C'est votre espace personnel sur FarmHub. Vous pouvez maintenant commencer à partager vos connaissances agricoles avec le monde entier.
            </p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left;">
                <div style="background: #f1f8e9; padding: 20px; border-radius: 12px;">
                    <i class="fas fa-seedling" style="color: var(--primary); font-size: 1.5rem; margin-bottom: 10px;"></i>
                    <h3 style="margin-bottom: 5px;">Mes Articles</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">0 articles publiés</p>
                </div>
                <div style="background: #fff3e0; padding: 20px; border-radius: 12px;">
                    <i class="fas fa-award" style="color: #ff9800; font-size: 1.5rem; margin-bottom: 10px;"></i>
                    <h3 style="margin-bottom: 5px;">Points Experts</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">10 points de bienvenue</p>
                </div>
            </div>

            <a href="index.php" class="btn btn-primary" style="margin-top: 40px; width: 100%; justify-content: center;">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>
    </div>

</body>
</html>
