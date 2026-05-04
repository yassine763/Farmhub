<?php
// google-login.php
require_once 'config/db.php';
require_once 'config/google-config.php';

// Générer l'URL de connexion Google
$login_url = $client->createAuthUrl();

// Redirection automatique pour une meilleure UX
header("Location: " . $login_url);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection vers Google - FarmHub</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="auth-wrapper">
    <div class="auth-card">
        <div class="logo" style="justify-content: center; margin-bottom: 20px;">🌾 FarmHub</div>
        <h2>Connexion Google</h2>
        <p>Nous vous redirigeons vers Google pour vous authentifier en toute sécurité...</p>
        <div class="loader"></div>
        <a href="<?php echo $login_url; ?>" class="btn-google" style="margin-top: 20px;">
            Si vous n'êtes pas redirigé, cliquez ici
        </a>
    </div>
</body>
</html>