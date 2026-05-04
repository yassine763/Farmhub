# FarmHub 🚜🌱

**FarmHub** is a web-based community platform designed to bridge the gap between agricultural enthusiasts, professional farmers, and experts. It provides a space to share knowledge, publish articles, and discuss modern agricultural techniques.

## 🚀 Key Features

- **Secure Authentication System**: 
  - Standard Email/Password registration with **Email Verification** (SMTP integration).
  - One-click **Google Login** using Google OAuth 2.0 API.
- **Interactive Content Feed**: 
  - Browse articles shared by the community.
  - Real-time interaction through **comments** and feedback.
- **Responsive Management**: 
  - Robust database schema for managing users, articles, and discussions.

## 🛠️ Tech Stack

- **Backend**: PHP (7.4+)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Integrations**: 
  - Google Client Library (OAuth)
  - PHPMailer / SMTP for email notifications
- **Environment**: XAMPP / Apache

## 📂 Project Structure

- `/config`: Configuration files for Google API and Mail settings.
- `/vendor`: Composer dependencies (Google API, etc.).
- `schema.sql`: Complete database structure (Users, Articles, Comments).
- `login.php`, `verify-code.php`: Authentication logic.
- `index.php`: Main feed and landing page.

## ⚙️ Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/farmhub.git
