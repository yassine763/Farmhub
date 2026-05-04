-- Database Schema for FallehiHub

-- Create Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) DEFAULT NULL, -- Nullable for Google Login
    google_id VARCHAR(100) DEFAULT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    role ENUM('farmer', 'expert', 'admin') DEFAULT 'farmer',
    is_verified BOOLEAN DEFAULT FALSE,
    verification_code VARCHAR(10) DEFAULT NULL,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_expiry DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    user_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create Comments table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    body TEXT NOT NULL,
    user_id INT,
    article_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Seed a Demo Farmer
INSERT INTO users (username, email, password, role, is_verified) VALUES
('demo_farmer', 'farmer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', TRUE);

-- Seed Sample Articles
INSERT INTO articles (title, body, image, user_id, status, views) VALUES
('Drip Irrigation Saved My Tomato Farm', 'After three years of struggling with water waste, I switched to drip irrigation. The results were immediate: 40% less water usage and 20% higher yield. Here is how I set it up...', 'https://images.unsplash.com/photo-1592910129841-e9455325510b?auto=format&fit=crop&q=80&w=800', 1, 'published', 152),
('Natural Pest Control with Neem Oil', 'Neem oil is a powerful natural pesticide. I have been using it on my kale and spinach with great success. It keeps away aphids without harming the bees.', 'https://images.unsplash.com/photo-1599940824399-b87987ceb72a?auto=format&fit=crop&q=80&w=800', 1, 'published', 89);
