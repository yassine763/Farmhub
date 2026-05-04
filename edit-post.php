<?php
// edit-post.php
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$article_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Vérifier si l'article existe et appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? AND user_id = ?");
$stmt->execute([$article_id, $user_id]);
$article = $stmt->fetch();

if (!$article) {
    die("Accès refusé ou article introuvable.");
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_article'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    
    if (!empty($title) && !empty($body)) {
        $update = $pdo->prepare("UPDATE articles SET title = ?, body = ? WHERE id = ?");
        $update->execute([$title, $body, $article_id]);
        
        $success = "Article mis à jour avec succès !";
        header("Refresh: 2; URL=index.php#" . $article_id);
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'article - FarmHub</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card" style="max-width: 600px;">
        <a href="index.php" style="color: var(--text-muted); display: block; text-align: left; margin-bottom: 20px;"><i class="fas fa-arrow-left"></i> Retour au flux</a>
        
        <h2>Modifier votre publication</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Titre</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>
            <div class="form-group">
                <label>Contenu</label>
                <textarea name="body" style="width: 100%; min-height: 200px; padding: 15px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit; font-size: 1rem;" required><?php echo htmlspecialchars($article['body']); ?></textarea>
            </div>
            <button type="submit" name="update_article" class="btn btn-primary btn-full">Enregistrer les modifications</button>
        </form>
    </div>

</body>
</html>
