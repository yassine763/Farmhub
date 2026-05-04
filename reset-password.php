<?php
// reset-password.php
require_once 'config/db.php';

$error = "";
$success = "";
$email = $_GET['email'] ?? "";
$token = $_GET['token'] ?? "";

// Vérifier si le token est toujours valide avant d'afficher la page
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$email, $token]);
$user = $stmt->fetch();

if (!$user) {
    die("Lien invalide ou expiré.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!empty($password)) {
        if ($password === $confirm) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe et effacer le token
            $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
            $update->execute([$hashed, $user['id']]);

            $success = "Votre mot de passe a été modifié avec succès !";
            header("Refresh: 2; URL=login.php");
        } else {
            $error = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $error = "Veuillez entrer un nouveau mot de passe.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - FarmHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">
    <div class="auth-card">
        <h2>Nouveau mot de passe</h2>
        <p>Choisissez un mot de passe sécurisé.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nouveau mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Changer le mot de passe</button>
        </form>
    </div>
</body>
</html>
