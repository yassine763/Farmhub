<?php
require_once 'config/db.php';

try {
    // 1. Créer quelques utilisateurs fictifs
    $users = [
        ['username' => 'Amine_Fermier', 'email' => 'amine@example.com', 'avatar' => 'https://i.pravatar.cc/150?u=amine'],
        ['username' => 'Sarah_Agro', 'email' => 'sarah@example.com', 'avatar' => 'https://i.pravatar.cc/150?u=sarah'],
        ['username' => 'Youssef_Expert', 'email' => 'youssef@example.com', 'avatar' => 'https://i.pravatar.cc/150?u=youssef'],
        ['username' => 'Fatima_Bio', 'email' => 'fatima@example.com', 'avatar' => 'https://i.pravatar.cc/150?u=fatima'],
    ];

    $pass = password_hash('password123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, email, password, avatar, is_verified) VALUES (?, ?, ?, ?, 1)");

    foreach ($users as $u) {
        $stmt->execute([$u['username'], $u['email'], $pass, $u['avatar']]);
    }

    // Récupérer les IDs des utilisateurs
    $userIds = $pdo->query("SELECT id FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $catIds = $pdo->query("SELECT id FROM categories")->fetchAll(PDO::FETCH_COLUMN);

    // 2. Créer des articles fictifs
    $articles = [
        [
            'title' => 'Astuce pour économiser l\'eau en été',
            'body' => 'J\'ai testé le paillage organique cette année sur mes courgettes. Non seulement le sol reste humide plus longtemps, mais j\'ai aussi beaucoup moins de mauvaises herbes ! Je recommande vivement pour ceux qui luttent avec la chaleur.',
            'image' => 'https://images.unsplash.com/photo-1585314062340-f1a5a7c9328d?auto=format&fit=crop&q=80&w=800',
            'user_id' => $userIds[array_rand($userIds)],
            'cat_id' => 1
        ],
        [
            'title' => 'Attention aux pucerons noirs !',
            'body' => 'Ils sont arrivés en force avec les dernières pluies. Un mélange de savon noir et d\'eau tiède pulvérisé tôt le matin a fait des miracles sur mes fèves. Ne laissez pas traîner, ils se multiplient vite !',
            'image' => 'https://images.unsplash.com/photo-1629807473010-096700f73809?auto=format&fit=crop&q=80&w=800',
            'user_id' => $userIds[array_rand($userIds)],
            'cat_id' => 3
        ],
        [
            'title' => 'Ma récolte de dattes cette année 🌴',
            'body' => 'La qualité est exceptionnelle grâce à une taille rigoureuse au printemps. Le sucre est bien concentré. Qui d\'autre a commencé la récolte dans le Sud ?',
            'image' => 'https://images.unsplash.com/photo-1541344999736-83eca8729b9e?auto=format&fit=crop&q=80&w=800',
            'user_id' => $userIds[array_rand($userIds)],
            'cat_id' => 5
        ],
        [
            'title' => 'Conseil de sol pour le Bio',
            'body' => 'N\'oubliez pas de faire des analyses de sol avant l\'automne. C\'est le meilleur moment pour corriger le pH avec de la chaux ou du compost bien mûr. Un bon sol, c\'est 80% du travail fait.',
            'image' => 'https://images.unsplash.com/photo-1592910129841-e9455325510b?auto=format&fit=crop&q=80&w=800',
            'user_id' => $userIds[array_rand($userIds)],
            'cat_id' => 4
        ]
    ];

    $stmtArt = $pdo->prepare("INSERT INTO articles (title, body, image, user_id, status) VALUES (?, ?, ?, ?, 'published')");
    $stmtLink = $pdo->prepare("INSERT INTO article_categories (article_id, category_id) VALUES (?, ?)");

    foreach ($articles as $a) {
        $stmtArt->execute([$a['title'], $a['body'], $a['image'], $a['user_id']]);
        $artId = $pdo->lastInsertId();
        $stmtLink->execute([$artId, $a['cat_id']]);
    }

    echo "Fake data inserted successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
