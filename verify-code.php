<?php
// verify-code.php
require_once 'config/db.php';

$error = "";
$email = $_GET['email'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $email = trim($_POST['email']);

    if (!empty($code) && !empty($email)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expiry > NOW()");
        $stmt->execute([$email, $code]);
        $user = $stmt->fetch();

        if ($user) {
            // Code valide ! Rediriger vers reset-password
            header("Location: reset-password.php?email=" . urlencode($email) . "&token=" . urlencode($code));
            exit;
        } else {
            $error = "Code invalide ou expiré.";
        }
    } else {
        $error = "Veuillez entrer le code.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification - FarmHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">
    <div class="auth-card">
        <h2>Vérification</h2>
        <p>Entrez le code de 6 chiffres envoyé à <strong><?php echo htmlspecialchars($email); ?></strong>.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="form-group">
                <label>Code de vérification</label>
                <input type="text" name="code" placeholder="123456" maxlength="6" style="text-align: center; letter-spacing: 5px; font-size: 1.5rem;" required>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Vérifier</button>
        </form>
        
        <div class="auth-footer">
            Vous n'avez pas reçu le code ? <a href="forgot-password.php">Renvoyer</a>
        </div>
    </div>
</body>
</html>
