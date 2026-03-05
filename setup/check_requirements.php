<?php
// This page checks if your computer/server has what it needs to run the app
?>
<!DOCTYPE html>
<html lang="en" class="<?= $currentTheme === 'dark' ? 'dark-theme' : 'light-theme' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Requirements Check</title>
    <link rel="icon" type="image/x-icon" href="../assets/favicon.ico">
    <script src="https://kit.fontawesome.com/0808479034.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon">🔧</div>
                <h1>System Requirements</h1>
                <p>Checking your server configuration</p>
            </div>

            <div style="space-y: 15px;">
                <?php
                $requirements = [
                    'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                    'PDO Extension' => extension_loaded('pdo'),
                    'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
                    'Session Support' => function_exists('session_start'),
                    'JSON Support' => function_exists('json_encode'),
                    'Password Hashing' => function_exists('password_hash'),
                ];

                $allPassed = true;
                foreach ($requirements as $requirement => $status) {
                    $allPassed = $allPassed && $status;
                    echo '<div style="display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid ' . ($status ? 'var(--success)' : 'var(--error)') . '; border-radius: 8px; margin-bottom: 10px; background: ' . ($status ? '#f0fdf4' : '#fef2f2') . ';">';
                    echo '<p>' . htmlspecialchars($requirement) . '</p>';
                    echo '<p style="color: ' . ($status ? 'var(--success)' : 'var(--error)') . '; font-weight: bold;">' . ($status ? '✓ PASS' : '✗ FAIL') . '</p>';
                    echo '</div>';
                }
                ?>

                <div style="margin-top: 20px; text-align: center;">
                    <?php if ($allPassed): ?>
                        <div class="alert alert-success">
                            🎉 All requirements met! Your system is ready.
                        </div>
                        <a href="install.php" class="btn btn-primary" style="margin-top: 15px;">
                            Continue to Installation
                        </a>
                    <?php else: ?>
                        <div class="alert alert-error">
                            <i class="fa-solid fa-xmark" style="color: var(--error);"></i> Some requirements are not met. Please install XAMPP or check your PHP configuration.
                        </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 30px; padding: 20px; background: var(--sec-gray-50); border-radius: 8px;">
                    <h3 style="margin-bottom: 15px; color: var(--sec-gray-900);">Current System Info:</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 8px;"><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                        <li style="margin-bottom: 8px;"><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
                        <li style="margin-bottom: 8px;"><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></li>
                        <li style="margin-bottom: 8px;"><strong>Current Directory:</strong> <?php echo __DIR__; ?></li>
                    </ul>
                </div>
            </div>

            <div class="auth-footer">
                <p style="text-align: center; color: var(--sec-gray-500); font-size: 0.9rem;">
                    For XAMPP users: Make sure Apache and MySQL services are running
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