<?php
// login.php
require_once 'config/db.php';

// Si déjà connecté, rediriger vers le dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: feed.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // 🐥 Rubber Duck: Hourra ! Le mot de passe est bon. 
            // On range l'ID et le nom de l'utilisateur dans la "Mémoire" (Session)
            // pour que le site sache qu'il est connecté sur toutes les autres pages.
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['avatar'] = $user['avatar'];
            
            header("Location: feed.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - FarmHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <a href="index.php" style="font-size: 1.5rem; font-weight: 700; color: var(--primary); display: block; margin-bottom: 30px;">🌾 FarmHub</a>
        
        <h2>Bienvenue !</h2>
        <p>Connectez-vous pour accéder à votre espace.</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="form-group" style="margin-bottom: 5px;">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="forgot-password.php" style="font-size: 0.85rem; color: var(--text-muted);">Mot de passe oublié ?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
        </form>

        <div class="divider">ou</div>

        <a href="google-login.php" class="btn-google">
            <img src="https://www.google.com/favicon.ico" width="18">
            Continuer avec Google
        </a>
        
        <div class="auth-footer">
            Pas encore de compte ? <a href="register.php">S'inscrire gratuitement</a>
        </div>
    </div>

</body>
</html>
