<?php
// google-callback.php
require_once 'config/db.php';
require_once 'config/google-config.php';

// Vérifier l'erreur
if (isset($_GET['error'])) {
    header('Location: login.php?error=google_auth_failed');
    exit;
}

// Vérifier le code
if (!isset($_GET['code'])) {
    header('Location: google-login.php');
    exit;
}

try {
    // Échanger le code contre un token
    $client->authenticate($_GET['code']);
    $token = $client->getAccessToken();
    
    // Récupérer les infos utilisateur (version v1)
    $oauth2 = new Google_Service_Oauth2($client);
    $user_info = $oauth2->userinfo->get();
    
    $google_id = $user_info->getId();
    $email = $user_info->getEmail();
    $name = $user_info->getName();
    $avatar = $user_info->getPicture();
    
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
    $stmt->execute([$email, $google_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Mettre à jour google_id si nécessaire
        if (empty($user['google_id'])) {
            $update = $pdo->prepare("UPDATE users SET google_id = ?, avatar = ? WHERE id = ?");
            $update->execute([$google_id, $avatar, $user['id']]);
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar'] ?: $avatar;
        
    } else {
        // Créer un nouvel utilisateur
        $username = strtolower(str_replace(' ', '_', $name));
        $original_username = $username;
        $counter = 1;
        
        // Vérifier si username existe déjà
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        while ($check->fetch()) {
            $username = $original_username . $counter;
            $check->execute([$username]);
            $counter++;
        }
        
        $insert = $pdo->prepare("INSERT INTO users (username, email, google_id, avatar, is_verified) VALUES (?, ?, ?, ?, 1)");
        $insert->execute([$username, $email, $google_id, $avatar]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['avatar'] = $avatar;
    }
    
    header('Location: feed.php');
    exit;
    
} catch (Exception $e) {
    error_log("Google Login Error: " . $e->getMessage());
    header('Location: login.php?error=google_error');
    exit;
}
?>