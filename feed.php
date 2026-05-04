<?php 
require_once 'config/db.php'; 

// Redirection si non connecté (le flux est réservé aux membres)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 🐥 Rubber Duck: Ici, on demande à la base de données de nous donner tous les articles.
// Mais attention ! On fait une "Grosse Requête" qui compte aussi les likes et les commentaires 
// pour chaque article en une seule fois. C'est plus efficace !
$stmt = $pdo->query("SELECT a.*, u.username, u.avatar as user_avatar,
                    (SELECT COUNT(*) FROM likes WHERE article_id = a.id) as likes_count,
                    (SELECT COUNT(*) FROM comments WHERE article_id = a.id) as comments_count,
                    (SELECT 1 FROM likes WHERE article_id = a.id AND user_id = " . ($_SESSION['user_id'] ?? 0) . ") as is_liked
                    FROM articles a 
                    JOIN users u ON a.user_id = u.id 
                    WHERE a.status = 'published'
                    ORDER BY a.created_at DESC");
$articles = $stmt->fetchAll();

// --- LOGIQUE ACTIONS (Like, Comment, Delete) ---

// 1. Gérer le LIKE
// 🐥 Rubber Duck: Si l'utilisateur clique sur "Like", on vérifie s'il l'a déjà fait.
// Si oui, on le retire (Unlike). Si non, on l'ajoute. C'est un bouton "Bascule" !
if (isset($_GET['action']) && $_GET['action'] === 'like' && isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    $check = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND article_id = ?");
    $check->execute([$user_id, $article_id]);
    
    if ($check->rowCount() > 0) {
        $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND article_id = ?")->execute([$user_id, $article_id]);
    } else {
        $pdo->prepare("INSERT INTO likes (user_id, article_id) VALUES (?, ?)")->execute([$user_id, $article_id]);
    }
    header("Location: feed.php#" . $article_id);
    exit;
}

// 2. Gérer le COMMENTAIRE
// 🐥 Rubber Duck: Quand quelqu'un écrit un commentaire, on l'enregistre avec son ID
// et l'ID de l'article pour savoir qui a dit quoi et sur quelle publication.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $article_id = (int)$_POST['article_id'];
    $body = trim($_POST['comment_body']);
    $user_id = $_SESSION['user_id'];

    if (!empty($body)) {
        $pdo->prepare("INSERT INTO comments (body, user_id, article_id) VALUES (?, ?, ?)")->execute([$body, $user_id, $article_id]);
    }
    header("Location: feed.php#" . $article_id);
    exit;
}

// 3. Gérer la SUPPRESSION
// 🐥 Rubber Duck: Pour supprimer, on vérifie TOUJOURS que l'utilisateur est bien le propriétaire.
// On ne veut pas que n'importe qui puisse effacer les articles des autres !
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $article_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Vérifier si l'utilisateur est le propriétaire
    $check = $pdo->prepare("SELECT id FROM articles WHERE id = ? AND user_id = ?");
    $check->execute([$article_id, $user_id]);
    
    if ($check->rowCount() > 0) {
        $pdo->prepare("DELETE FROM articles WHERE id = ?")->execute([$article_id]);
    }
    header("Location: feed.php?msg=deleted");
    exit;
}

// Gérer la publication d'un nouvel article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publish_article'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $user_id = $_SESSION['user_id'];
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/articles/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('post_') . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    if (!empty($title) && !empty($body)) {
        $insert = $pdo->prepare("INSERT INTO articles (title, body, image, user_id, status) VALUES (?, ?, ?, ?, 'published')");
        $insert->execute([$title, $body, $image_path, $user_id]);
        header("Location: feed.php?success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flux Communautaire - FarmHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="background-color: var(--bg-cream);">

    <header>
        <div class="container">
            <nav>
                <a href="index.php" class="logo">🌾 FarmHub</a>
                <ul class="nav-links">
                    <li><a href="feed.php" class="active"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Communauté</a></li>
                </ul>
                <div class="nav-btns">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <a href="dashboard.php">
                            <img src="<?php echo $_SESSION['avatar'] ?: 'https://ui-avatars.com/api/?name='.$_SESSION['username']; ?>" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
                        </a>
                        <a href="logout.php" class="btn btn-outline" style="padding: 8px 20px; font-size: 0.9rem;">Déconnexion</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="container" style="margin-top: 20px;">
            <div class="success-msg" style="text-align: center;">🎉 Votre article a été publié avec succès !</div>
        </div>
    <?php endif; ?>

    <main class="container">
        <div class="feed-layout">
            <aside class="feed-sidebar">
                <div class="sidebar-card">
                    <div style="text-align: center; margin-bottom: 20px;">
                        <img src="<?php echo $_SESSION['avatar'] ?: 'https://ui-avatars.com/api/?name='.$_SESSION['username']; ?>" style="width: 80px; height: 80px; border-radius: 50%; margin-bottom: 10px; border: 3px solid var(--primary);">
                        <h4><?php echo htmlspecialchars($_SESSION['username']); ?></h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted);">Agriculteur Passionné</p>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                    <ul class="trending-list">
                        <li><a href="dashboard.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                        <li><a href="#"><i class="fas fa-bookmark"></i> Enregistrements</a></li>
                    </ul>
                </div>
            </aside>

            <section class="feed-main">
                <div class="post-card" style="padding: 25px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.2rem; color: var(--primary);">Partagez un conseil ou une question</h3>
                    <form action="feed.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="text" name="title" placeholder="Titre de votre publication..." required style="border: none; border-bottom: 1px solid #eee; border-radius: 0; padding: 10px 0; font-weight: 700; font-size: 1.1rem;">
                        </div>
                        <div class="form-group">
                            <textarea name="body" placeholder="Quoi de neuf sur votre ferme, <?php echo htmlspecialchars($_SESSION['username']); ?> ?" required style="width: 100%; border: none; min-height: 100px; resize: none; font-family: inherit; font-size: 1rem; padding: 10px 0; outline: none;"></textarea>
                        </div>
                        <div style="display: flex; gap: 15px; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f1f1;">
                            <label for="image-upload" style="cursor: pointer; color: var(--text-muted); display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 600; flex: 1;">
                                <i class="fas fa-image" style="color: #4caf50; font-size: 1.2rem;"></i> Ajouter une photo
                                <input type="file" id="image-upload" name="image" accept="image/*" style="display: none;">
                            </label>
                            <button type="submit" name="publish_article" class="btn btn-primary" style="padding: 10px 30px;">Publier</button>
                        </div>
                    </form>
                </div>

                <?php foreach ($articles as $article): 
                    $comm_stmt = $pdo->prepare("SELECT c.*, u.username, u.avatar FROM comments c JOIN users u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.created_at ASC");
                    $comm_stmt->execute([$article['id']]);
                    $article_comments = $comm_stmt->fetchAll();
                ?>
                <article class="post-card" id="<?php echo $article['id']; ?>">
                    <div class="post-header">
                        <img src="<?php echo $article['user_avatar'] ?: 'https://ui-avatars.com/api/?name='.$article['username']; ?>" class="post-user-avatar">
                        <div class="post-user-info" style="flex: 1;">
                            <h4><?php echo htmlspecialchars($article['username']); ?></h4>
                            <span><?php echo date('d M, Y', strtotime($article['created_at'])); ?></span>
                        </div>
                        <?php if ($_SESSION['user_id'] == $article['user_id']): ?>
                        <div class="post-menu" style="position: relative;">
                            <button onclick="this.nextElementSibling.classList.toggle('active')" style="background: none; border: none; cursor: pointer; color: var(--text-muted);"><i class="fas fa-ellipsis-h"></i></button>
                            <div class="dropdown-menu" style="position: absolute; right: 0; top: 100%; background: white; border-radius: 8px; box-shadow: var(--shadow); z-index: 10; display: none; min-width: 120px;">
                                <a href="edit-post.php?id=<?php echo $article['id']; ?>" style="display: block; padding: 10px 15px; font-size: 0.9rem; color: var(--text-main);"><i class="fas fa-edit"></i> Modifier</a>
                                <a href="feed.php?action=delete&id=<?php echo $article['id']; ?>" onclick="return confirm('Supprimer cet article ?')" style="display: block; padding: 10px 15px; font-size: 0.9rem; color: #e74c3c;"><i class="fas fa-trash"></i> Supprimer</a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="post-content">
                        <h3 style="margin-bottom: 10px;"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($article['body'])); ?></p>
                        <?php if ($article['image']): ?>
                            <img src="<?php echo $article['image']; ?>" class="post-image">
                        <?php endif; ?>
                    </div>
                    <div class="post-actions">
                        <a href="feed.php?action=like&id=<?php echo $article['id']; ?>" class="action-btn <?php echo $article['is_liked'] ? 'liked' : ''; ?>" style="text-decoration: none;">
                            <i class="<?php echo $article['is_liked'] ? 'fas' : 'far'; ?> fa-thumbs-up"></i> <?php echo $article['likes_count']; ?>
                        </a>
                        <button class="action-btn" onclick="document.getElementById('comments-<?php echo $article['id']; ?>').classList.toggle('active')">
                            <i class="far fa-comment"></i> <?php echo $article['comments_count']; ?>
                        </button>
                    </div>
                    <div class="comments-section" id="comments-<?php echo $article['id']; ?>" style="padding: 0 20px 20px; display: none; background: #fafafa; border-top: 1px solid #f1f1f1;">
                        <div style="padding: 15px 0;">
                            <?php foreach ($article_comments as $comment): ?>
                                <div style="display: flex; gap: 10px; margin-bottom: 12px;">
                                    <img src="<?php echo $comment['avatar'] ?: 'https://ui-avatars.com/api/?name='.$comment['username']; ?>" style="width: 30px; height: 30px; border-radius: 50%;">
                                    <div style="background: #f0f2f5; padding: 8px 12px; border-radius: 12px; font-size: 0.9rem; flex: 1;">
                                        <div style="font-weight: 700;"><?php echo htmlspecialchars($comment['username']); ?></div>
                                        <div><?php echo nl2br(htmlspecialchars($comment['body'])); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form action="feed.php" method="POST" style="display: flex; gap: 10px;">
                            <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                            <input type="text" name="comment_body" placeholder="Écrire un commentaire..." required style="flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 5px 15px; font-size: 0.9rem; outline: none;">
                            <button type="submit" name="add_comment" style="background: none; border: none; color: var(--primary); cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                </article>
                <?php endforeach; ?>
            </section>
        </div>
    </main>

    <style>
        .dropdown-menu.active, .comments-section.active { display: block !important; }
    </style>
</body>
</html>
