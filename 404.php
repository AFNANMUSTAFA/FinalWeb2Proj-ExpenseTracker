<?php
// Tell the browser this page was not found
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en" class="<?= $currentTheme === 'dark' ? 'dark-theme' : 'light-theme' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - Expense Tracker</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://kit.fontawesome.com/0808479034.js" crossorigin="anonymous"></script>
    </head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon"><i class="fa-solid fa-xmark" style="color: var(--error);"></i></div>
                <h1>Page Not Found</h1>
                <p>The page you're looking for doesn't exist</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p style="color: var(--sec-gray-600); margin-bottom: 20px;">
                    The page you requested could not be found. It may have been moved, deleted, or you entered the wrong URL.
                </p>
                
                <a href="index.php" class="btn btn-primary">
                    <i class="fa-solid fa-house"></i> Go to Homepage
                </a>
            </div>

            <div class="auth-footer">
                <p style="text-align: center; color: var(--sec-gray-500); font-size: 0.9rem;">
                    If you believe this is an error, please contact support
                </p>
            </div>
        </div>
    </div>

</body>
</html> 