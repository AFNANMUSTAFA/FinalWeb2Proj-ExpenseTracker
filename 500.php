<?php
// Tell the browser this is a server error
http_response_code(500);
?>
<!DOCTYPE html>
<html lang="en" class="<?= $currentTheme === 'dark' ? 'dark-theme' : 'light-theme' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - Expense Tracker</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://kit.fontawesome.com/0808479034.js" crossorigin="anonymous"></script>
    </head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="logo-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <h1>Server Error</h1>
                <p>Something went wrong on our end</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p style="color: var(--sec-gray-600); margin-bottom: 20px;">
                    We're experiencing some technical difficulties. Please try again in a few moments.
                </p>
                
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="history.back()" class="btn btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Go Back
                    </button>
                    <a href="index.php" class="btn btn-primary">
                        <i class="fa-solid fa-house"></i> Homepage
                    </a>
                </div>
            </div>

            <div class="auth-footer">
                <p style="text-align: center; color: var(--sec-gray-500); font-size: 0.9rem;">
                    Error Code: 500 | If the problem persists, please contact support
                </p>
            </div>
        </div>
    </div>

</body>
</html>