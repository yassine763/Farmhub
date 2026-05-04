<?php
// forgot-password.php
require_once 'config/db.php';
require_once 'config/mail-config.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Générer un code de 6 chiffres
            $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Sauvegarder dans la DB
            $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $update->execute([$code, $expiry, $user['id']]);

            // Envoyer l'email
            $subject = "Réinitialisation de votre mot de passe - FarmHub";
            $message = "
                <div style='font-family: Arial, sans-serif; padding: 20px; color: #333;'>
                    <h2 style='color: #2e7d32;'>🌾 FarmHub</h2>
                    <p>Bonjour <strong>{$user['username']}</strong>,</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                    <p>Voici votre code de vérification (valable 1 heure) :</p>
                    <div style='background: #f1f8e9; padding: 15px; font-size: 24px; font-weight: bold; text-align: center; color: #2e7d32; border-radius: 10px; border: 2px dashed #2e7d32;'>
                        $code
                    </div>
                    <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
                </div>
            ";

            if (sendEmail($email, $subject, $message)) {
                $success = "Un code a été envoyé à votre adresse email.";
                header("Refresh: 2; URL=verify-code.php?email=" . urlencode($email));
            } else {
                // Pour le test local, si l'email échoue, on affiche le code (optionnel)
                $error = "Erreur lors de l'envoi de l'email. Vérifiez votre config SMTP.";
                // $success = "DEBUG: Le code est $code (Email non envoyé)"; 
            }
        } else {
            $error = "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        $error = "Veuillez entrer votre email.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - FarmHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">
    <div class="auth-card">
        <a href="login.php" style="color: var(--text-muted); display: block; text-align: left; margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Retour</a>
        <h2>Mot de passe oublié</h2>
        <p>Entrez votre email pour recevoir un code de réinitialisation.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Envoyer le code</button>
        </form>
    </div>
</body>
</html>
