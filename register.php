<?php
// register.php
require_once 'config/db.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        // Vérifier si l'utilisateur ou l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Le nom d'utilisateur ou l'email est déjà utilisé.";
        } else {
            // Hasher le mot de passe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Générer code de vérification
            $v_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Insérer l'utilisateur
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, verification_code) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$username, $email, $hashedPassword, $v_code]);
                
                // Envoyer l'email de bienvenue
                require_once 'config/mail-config.php';
                $subject = "Bienvenue sur FarmHub - Vérifiez votre compte";
                $message = "
                    <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                        <h2 style='color: #2e7d32;'>🌱 Bienvenue sur FarmHub !</h2>
                        <p>Merci de nous avoir rejoint, <strong>$username</strong>.</p>
                        <p>Voici votre code de vérification pour activer votre compte :</p>
                        <div style='background: #f1f8e9; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; color: #2e7d32; border-radius: 10px; border: 2px dashed #2e7d32;'>
                            $v_code
                        </div>
                    </div>
                ";
                
                sendEmail($email, $subject, $message);
                
                $success = "Inscription réussie ! Un code de vérification vous a été envoyé.";
                header("Refresh: 2; URL=verify-email.php?email=" . urlencode($email));
            } catch (PDOException $e) {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
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
    <title>Inscription - FarmHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <a href="index.php" style="font-size: 1.5rem; font-weight: 700; color: var(--primary); display: block; margin-bottom: 30px;">🌾 FarmHub</a>
        
        <h2>Créer un compte</h2>
        <p>Rejoignez la plus grande communauté de partage agricole.</p>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" placeholder="ex: JeanFermier" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Créer mon compte</button>
        </form>

        <div class="divider">ou</div>

        <a href="google-login.php" class="btn-google">
            <img src="https://www.google.com/favicon.ico" width="18">
            S'inscrire avec Google
        </a>
        
        <div class="auth-footer">
            Déjà un compte ? <a href="login.php">Se connecter</a>
        </div>
    </div>

</body>
</html>
