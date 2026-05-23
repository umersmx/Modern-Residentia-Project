# 🏰 Modern Residentia — Premium Property Rental & Management Platform

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.0-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Bootstrap Version](https://img.shields.io/badge/Bootstrap-5.3.3-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Design System](https://img.shields.io/badge/Design_System-Premium_Luxury-D4A853?style=for-the-badge)](https://github.com/umersmx)
[![License: MIT](https://img.shields.io/badge/License-MIT-10B981?style=for-the-badge)](https://opensource.org/licenses/MIT)

Modern Residentia is a **luxury real estate property rental and management application**. Built with a dark-first design philosophy, this platform streamlines the workflow between property renters, owners, and administrators while delivering a modern, high-end web experience.

---

## 🌟 Key Features

### 👤 Role-Based Access Control (RBAC)
The platform manages three secure access levels, each with its own personalized dashboard:
*   **Administrator**: Manages listings, handles property verification (approving/rejecting ads), reviews custom renter request lists, and controls user accounts.
*   **Property Owner**: Can publish and manage properties, upload multiple image galleries, view ad analytics (views and active inquiries), and chat directly with potential renters.
*   **Renter**: Can browse listings, filter properties in real-time, save listings to their favorites dashboard, send direct property inquiries to owners, and submit custom housing requests to administrators.

### 🎨 Premium Design System
*   **Luxury Theme**: Dark-first glassmorphic UI with vibrant emerald-gold accents. Fully responsive and visually cohesive.
*   **Micro-interactions**: 3D card tilt on hover, animated counters, smooth parallax hero background, and fade-in animations.
*   **Smart Dark/Light Mode**: Smooth transitions between a sleek dark configuration and a clean, high-contrast light setup.

### 🔍 Search & Filtering
*   **Collapsible Search Bar**: Built with intuitive layouts that fit neatly above the fold.
*   **Real-time Sidebar Filters**: Instant filtering by property type, price limits, bedroom counts, and sorting parameters. Responsive sidebar collapses into a clean filter menu on mobile devices.

### 💬 Instant Inquiry System
*   **Direct Messaging**: Direct communication channel between renters and owners.
*   **Unified Chat Dashboard**: Message threads are organized per property. Real-time updates with customizable indicators.

---

## 🛠️ Technology Stack

| Layer | Technologies |
| :--- | :--- |
| **Frontend** | HTML5, CSS3 (Vanilla + Custom Luxury Tokens), JavaScript (ES6+), jQuery, Bootstrap 5.3, FontAwesome 6, Animate.css |
| **Backend** | PHP 8.x (Procedural MVC, Session Auth, Secure File Management) |
| **Database** | MySQL (Relational tables, prepared statements, automatic cascades) |
| **Security** | BCrypt Password Hashing, Parameterized MySQLi Queries, Role Authentication Guards, HTML Sanitization |

---

## 📂 Project Structure

```text
/WEB Project
├── actions/             # Form submission handlers (auth, properties, chat, etc.)
├── ajax/                # AJAX endpoints (favorites, filters)
├── assets/
│   ├── css/             # Stylesheets (style.css - contains all luxury variables)
│   ├── js/              # JavaScript behaviors (main.js - animations, chat, theme toggles)
│   └── images/          # Image assets & custom generated hero/auth background layers
├── config/              # Configuration (Database connection, App constants)
├── includes/            # Template fragments (header, footer, navbar)
├── uploads/             # User-uploaded assets (avatars, property pictures)
│   └── avatars/         # Saved user profile pictures
└── database.sql         # Base database structure and mock data
```

---

## 🚀 Setup & Installation

### Prerequisites
*   Local server stack (e.g. **XAMPP**, **WAMP**, or **MAMP**).
*   PHP 8.0+ and MySQL 5.7+ installed.

### Steps
1.  **Clone / Download the Repository**:
    Extract the codebase into your server's root folder (e.g., `C:/xampp/htdocs/WEB Project`).
2.  **Initialize the Database**:
    *   Open your MySQL manager (e.g., phpMyAdmin).
    *   Create a new database named `modern_residentia`.
    *   Import the SQL database file located at `config/database.sql` (or `database.sql` in the root).
3.  **Verify DB Connection**:
    Open `config/db_connection.php` and verify the credentials match your local MySQL configuration:
    ```php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "modern_residentia";
    ```
4.  **Local Access**:
    Launch your server stack and navigate to:
    `http://localhost/WEB Project/`

---

## 📱 Mobile Network Testing (WiFi Connection)

Modern Residentia features **dynamic base URL generation** that enables you to test responsive layouts on physical mobile devices on your local network:

1.  **Find Your PC's Local IP**:
    Open Command Prompt (`cmd`) and type `ipconfig`. Locate your IPv4 Address (e.g. `192.168.1.15`).
2.  **Access on Phone**:
    Ensure your phone is connected to the **same WiFi network** as your PC, then navigate to:
    `http://192.168.1.15/WEB Project/`
3.  **Firewall Notice**:
    If the connection times out, add an inbound firewall exception for port `80` (or allow XAMPP Apache through Windows Defender Firewall).

---

## 🛡️ Security Implementations

*   **Anti-SQLi**: Bound parameters using prepared mysqli queries block all SQL injection attempts.
*   **Bcrypt Hashing**: Passwords are securely hashed with a salt factor before DB insertions.
*   **XSS Mitigation**: Variables rendered inside HTML outputs are escaped using `htmlspecialchars()` contexts.
*   **Role Protection**: Unauthorized users attempting to request dashboard pages or trigger actions are blocked by session-based guards.

---

## 📝 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Developed with ❤️ by [Umer Farooq](https://github.com/umersmx) , [Mustafa Khizar](https://github.com/KhizarDoingProgramming) for Academic & Portfolio Excellence.**
