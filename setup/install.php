<?php
// This page helps you set up the app for the first time
session_start();

// Don't redirect if already installed; just show the page
$isInstalled = file_exists('../config/.installed');

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = $_POST['db_host'] ?? 'localhost';
    $dbUser = $_POST['db_user'] ?? 'root';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? 'expense_tracker';

    try {
        // Try to connect to the database
        $pdo = new PDO("mysql:host=$dbHost;charset=utf8mb4", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Make the database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE $dbName");

        // Make the tables for users and expenses
        $pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_username (username)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS expenses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                category VARCHAR(50) NOT NULL,
                expense_date DATE NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_expense_date (expense_date),
                INDEX idx_category (category),
                INDEX idx_user_date (user_id, expense_date),
                INDEX idx_user_category (user_id, category)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ');

        // Save the database info for the app to use
        $configContent = <<<PHP
        
        // Info for connecting to the database
        // Edit these if your database is different

        define('DB_HOST', '$dbHost');
        define('DB_USER', '$dbUser');
        define('DB_PASS', '$dbPass');
        define('DB_NAME', '$dbName');

        // This function connects to the database and gives you the connection
        function getConnection() {
            try {
                \$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
                \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                \$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                \$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                return \$pdo;
            } catch(PDOException \$e) {
                die("Connection failed: " . \$e->getMessage());
            }
        }

        PHP;

        file_put_contents('../config/database.php', "<?php" . $configContent . "?>");
        file_put_contents('../config/.installed', date('Y-m-d H:i:s'));

        $message = 'Installation completed successfully! You can now use the application.';
        $messageType = 'success';

        // After 3 seconds, send you to the homepage
        header('refresh:3;url=../index.php');
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="<?= $currentTheme === 'dark' ? 'dark-theme' : 'light-theme' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker - Installation</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <script src="https://kit.fontawesome.com/0808479034.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon"><i class="fa-solid fa-sack-dollar" style="color: white;"></i></div>
                <h1>Setup Expense Tracker</h1>
                <p>Configure your database connection</p>
            </div>

            <?php if ($isInstalled): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-info" style="color: var(--success);"></i>
                    The application is already installed. If you want to reinstall, please delete <code>config/.installed</code> and <code>config/database.php</code> first.
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (!$isInstalled && $messageType !== 'success'): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="db_host" class="form-label">Database Host</label>
                        <input type="text" id="db_host" name="db_host" class="form-input" 
                               value="localhost" required>
                        <small style="color: var(--sec-gray-500); font-size: 0.8rem;">Usually 'localhost' for XAMPP</small>
                    </div>

                    <div class="form-group">
                        <label for="db_user" class="form-label">Database Username</label>
                        <input type="text" id="db_user" name="db_user" class="form-input" 
                               value="root" required>
                        <small style="color: var(--sec-gray-500); font-size: 0.8rem;">Default is 'root' for XAMPP</small>
                    </div>

                    <div class="form-group">
                        <label for="db_pass" class="form-label">Database Password</label>
                        <input type="password" id="db_pass" name="db_pass" class="form-input" 
                               placeholder="Leave empty for XAMPP default">
                        <small style="color: var(--sec-gray-500); font-size: 0.8rem;">Usually empty for XAMPP</small>
                    </div>

                    <div class="form-group">
                        <label for="db_name" class="form-label">Database Name</label>
                        <input type="text" id="db_name" name="db_name" class="form-input" 
                               value="expense_tracker" required>
                        <small style="color: var(--sec-gray-500); font-size: 0.8rem;">Will be created automatically</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-rocket"></i> Install Application
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-footer">
                <p style="text-align: center; color: var(--sec-gray-500); font-size: 0.9rem;">
                    Make sure XAMPP MySQL service is running before installation
                </p>
            </div>
        </div>
    </div>
    <!-- Footer section at the bottom of the page -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div style="font-size: 0.8rem; opacity: 0.9;">Code With <i class="fa-solid fa-heart" style="color: var(--error);"></i> by <a href="https://salman.is-a.dev" target="_blank">Eng. Salman Abualhin</a></div>
            </div>
        </div>
    </footer>
</body>
</html>