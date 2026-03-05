<?php
session_start();
require_once 'config/database.php';

// --- User sign up, login, and logout ---
function register($username, $email, $password)
{
    $pdo = getConnection();

    // Check if the email is already used
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }

    // Save the new user with a safe password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');

    if ($stmt->execute([$username, $email, $hashedPassword])) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function login($email, $password)
{
    $pdo = getConnection();

    $stmt = $pdo->prepare('SELECT id, username, email, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the password is right
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        return ['success' => true, 'message' => 'Login successful'];
    } else {
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
}

function logout()
{
    // Log the user out and send them to the login page
    session_destroy();
    header('Location: index.php');
    exit();
}

function isLoggedIn()
{
    // Check if the user is logged in
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    // If not logged in, send to login page
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

// --- Expense functions ---
function addExpense($userId, $title, $amount, $category, $expenseDate, $description = '')
{
    $pdo = getConnection();

    // Add a new expense to the database
    $stmt = $pdo->prepare('INSERT INTO expenses (user_id, title, amount, category, expense_date, description) VALUES (?, ?, ?, ?, ?, ?)');

    if ($stmt->execute([$userId, $title, $amount, $category, $expenseDate, $description])) {
        return ['success' => true, 'message' => 'Expense added successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to add expense'];
    }
}

function getExpenses($userId, $filters = []) {
    $pdo = getConnection();

    // Get all expenses for this user, with filters if needed
    $sql = 'SELECT * FROM expenses WHERE user_id = ?';
    $params = [$userId];

    if (!empty($filters['category'])) {
        $sql .= ' AND category = ?';
        $params[] = $filters['category'];
    }

    if (!empty($filters['month'])) {
        $sql .= ' AND MONTH(expense_date) = ?';
        $params[] = $filters['month'];
    }

    if (!empty($filters['year'])) {
        $sql .= ' AND YEAR(expense_date) = ?';
        $params[] = $filters['year'];
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND title LIKE ?';
        $params[] = '%' . $filters['search'] . '%';
    }

    $sql .= ' ORDER BY expense_date DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateExpense($id, $userId, $title, $amount, $category, $expenseDate, $description = '')
{
    $pdo = getConnection();

    // Update an expense
    $stmt = $pdo->prepare('UPDATE expenses SET title = ?, amount = ?, category = ?, expense_date = ?, description = ? WHERE id = ? AND user_id = ?');

    if ($stmt->execute([$title, $amount, $category, $expenseDate, $description, $id, $userId])) {
        return ['success' => true, 'message' => 'Expense updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to update expense'];
    }
}

function deleteExpense($id, $userId)
{
    $pdo = getConnection();

    // Delete an expense
    $stmt = $pdo->prepare('DELETE FROM expenses WHERE id = ? AND user_id = ?');

    if ($stmt->execute([$id, $userId])) {
        return ['success' => true, 'message' => 'Expense deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to delete expense'];
    }
}

function getExpenseCategories()
{
    // List of categories you can use
    return [
        'Food & Dining',
        'Transportation',
        'Shopping',
        'Entertainment',
        'Bills & Utilities',
        'Healthcare',
        'Education',
        'Travel',
        'Other'
    ];
}

function getMonthlySummary($userId, $year = null)
{
    $pdo = getConnection();

    // If no year is given, use this year
    if (!$year) {
        $year = date('Y');
    }

    // Get how much you spent each month
    $stmt = $pdo->prepare('
        SELECT 
            MONTH(expense_date) as month,
            MONTHNAME(expense_date) as month_name,
            SUM(amount) as total,
            COUNT(*) as count
        FROM expenses 
        WHERE user_id = ? AND YEAR(expense_date) = ?
        GROUP BY MONTH(expense_date), MONTHNAME(expense_date)
        ORDER BY MONTH(expense_date)
    ');

    $stmt->execute([$userId, $year]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCategorySummary($userId, $year = null)
{
    $pdo = getConnection();

    // If no year is given, use this year
    if (!$year) {
        $year = date('Y');
    }

    // Get how much you spent in each category
    $stmt = $pdo->prepare('
        SELECT 
            category,
            SUM(amount) as total,
            COUNT(*) as count
        FROM expenses 
        WHERE user_id = ? AND YEAR(expense_date) = ?
        GROUP BY category
        ORDER BY total DESC
    ');

    $stmt->execute([$userId, $year]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTotalExpenses($userId, $period = 'all')
{
    $pdo = getConnection();

    // Get the total amount you spent (all time, this month, or this year)
    $sql = 'SELECT SUM(amount) as total FROM expenses WHERE user_id = ?';
    $params = [$userId];

    switch ($period) {
        case 'month':
            $sql .= ' AND MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())';
            break;
        case 'year':
            $sql .= ' AND YEAR(expense_date) = YEAR(CURRENT_DATE())';
            break;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'] ?? 0;
}

// Handle theme switching
if (isset($_GET['theme'])) {
    $theme = $_GET['theme'] === 'dark' ? 'dark' : 'light';
    setcookie('theme', $theme, time() + (86400 * 30), '/');  // Save for 30 days
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));  // Redirect without query param
    exit();
}

// Get current theme
$currentTheme = $_COOKIE['theme'] ?? 'light';
?>