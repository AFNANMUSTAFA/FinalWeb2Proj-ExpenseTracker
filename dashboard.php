<?php
// Make sure the user is logged in before showing the dashboard
require_once 'includes/functions.php';

// Require login to access dashboard
requireLogin();

$message = '';
$messageType = '';
// Figure out which tab should be active (default is overview)
$activeTab = $_GET['tab'] ?? 'overview';

// Handle any forms the user submits (like adding, editing, or deleting expenses)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_expense':
            $title = trim($_POST['title']);
            $amount = floatval($_POST['amount']);
            $category = $_POST['category'];
            $expenseDate = $_POST['expense_date'];
            $description = trim($_POST['description']);

            // Check if all required fields are filled in
            if (empty($title) || $amount <= 0 || empty($category) || empty($expenseDate)) {
                $message = 'Please fill in all required fields';
                $messageType = 'error';
            } else {
                // Try to add the expense and show a message
                $result = addExpense($_SESSION['user_id'], $title, $amount, $category, $expenseDate, $description);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                if ($result['success']) {
                    $activeTab = 'expenses';
                }
            }
            break;

        case 'update_expense':
            // Update an existing expense
            $expenseId = intval($_POST['expense_id']);
            $title = trim($_POST['title']);
            $amount = floatval($_POST['amount']);
            $category = $_POST['category'];
            $expenseDate = $_POST['expense_date'];
            $description = trim($_POST['description']);

            $result = updateExpense($expenseId, $_SESSION['user_id'], $title, $amount, $category, $expenseDate, $description);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            $activeTab = 'expenses';
            break;

        case 'delete_expense':
            // Delete an expense
            $expenseId = intval($_POST['expense_id']);
            $result = deleteExpense($expenseId, $_SESSION['user_id']);
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            $activeTab = 'expenses';
            break;
    }
}

// Get any filters the user set (like category, month, year, or search)
$filters = [];
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters['category'] = $_GET['category'];
}
if (isset($_GET['month']) && !empty($_GET['month'])) {
    $filters['month'] = $_GET['month'];
}
if (isset($_GET['year']) && !empty($_GET['year'])) {
    $filters['year'] = $_GET['year'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = $_GET['search'];
}

// Get all the data needed for the dashboard
$expenses = getExpenses($_SESSION['user_id'], $filters);
$categories = getExpenseCategories();
$totalThisMonth = getTotalExpenses($_SESSION['user_id'], 'month');
$totalThisYear = getTotalExpenses($_SESSION['user_id'], 'year');
$totalAllTime = getTotalExpenses($_SESSION['user_id'], 'all');
$monthlySummary = getMonthlySummary($_SESSION['user_id']);
$categorySummary = getCategorySummary($_SESSION['user_id']);

// Figure out pagination for the expenses list
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$totalExpenses = count($expenses);
$totalPages = ceil($totalExpenses / $perPage);
$offset = ($page - 1) * $perPage;
$paginatedExpenses = array_slice($expenses, $offset, $perPage);
?>

<!DOCTYPE html>
<html lang="en" class="<?= $currentTheme === 'dark' ? 'dark-theme' : 'light-theme' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Expense Tracker - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="./assets/favicon.ico">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://kit.fontawesome.com/0808479034.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Header section at the top of the page -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon"><i class="fa-solid fa-sack-dollar"></i></div>
                    <div>
                        <div>Expense Tracker</div>
                        <div style="font-size: 0.8rem; opacity: 0.9;">Manage your finances</div>
                    </div>
                </div>
                
                <div class="user-info">
                    <div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>

                    <div>
                        <form method="get" style="display: inline;">
                            <input type="hidden" name="theme" value="<?= $currentTheme === 'dark' ? 'light' : 'dark' ?>">
                            <button class="btn btn-outline" style="color: var(--white); border-color: rgba(255,255,255,0.3);" type="submit" title="Switch Theme">
                                <i class="fa-solid <?= $currentTheme === 'dark' ? 'fa-sun' : 'fa-moon' ?>"></i>
                            </button>
                        </form>
                        <a href="logout.php" class="btn btn-outline" style="color: var(--white); border-color: rgba(255,255,255,0.3);">
                            <i class="fa-solid fa-arrow-right-from-bracket" style="color: white;"></i> Logout
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </header>

    <!-- Navigation bar for switching between tabs -->
    <nav class="nav">
        <div class="container">
            <div class="nav-tabs">
                <a href="?tab=overview" class="nav-tab <?php echo $activeTab === 'overview' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-house"></i> Overview
                </a>
                <a href="?tab=add" class="nav-tab <?php echo $activeTab === 'add' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus"></i> Add Expense
                </a>
                <a href="?tab=expenses" class="nav-tab <?php echo $activeTab === 'expenses' ? 'active' : ''; ?>">
                    <i class="fa-regular fa-clipboard"></i> My Expenses
                </a>
                <a href="?tab=reports" class="nav-tab <?php echo $activeTab === 'reports' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-simple"></i> Reports
                </a>
            </div>
        </div>
    </nav>

    <!-- Main content area where everything shows up -->
    <main class="main-content">
        <div class="container">
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($activeTab === 'overview'): ?>
                <!-- Overview Tab: Quick stats and recent expenses -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>This Month</h3>
                            <div class="amount">$<?php echo number_format($totalThisMonth, 2); ?></div>
                        </div>
                        <div class="stat-icon orange"><i class="fa-solid fa-credit-card" style="color: var(--primary-orange);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>This Year</h3>
                            <div class="amount">$<?php echo number_format($totalThisYear, 2); ?></div>
                        </div>
                        <div class="stat-icon blue"><i class="fa-solid fa-calendar" style="color: var(--primary-blue);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total Expenses</h3>
                            <div class="amount"><?php echo count($expenses); ?></div>
                        </div>
                        <div class="stat-icon dark"><i class="fa-solid fa-chart-simple" style="color: var(--primary-dark);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>All Time</h3>
                            <div class="amount">$<?php echo number_format($totalAllTime, 2); ?></div>
                        </div>
                        <div class="stat-icon success"><i class="fa-solid fa-sack-dollar" style="color: var(--success   );"></i></div>
                    </div>
                </div>

                <!-- Recent Expenses Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon orange"><i class="fa-regular fa-clipboard"></i></div>
                        <div>
                            <div class="card-title">Recent Expenses</div>
                            <div class="card-subtitle">Your latest transactions</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (count($expenses) > 0): ?>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Amount</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($expenses, 0, 5) as $expense): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($expense['title']); ?></td>
                                                <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                                                <td><span class="category-badge"><?php echo htmlspecialchars($expense['category']); ?></span></td>
                                                <td><?php echo date('M j, Y', strtotime($expense['expense_date'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--sec-gray-500); padding: 40px;">
                                No expenses recorded yet. <a href="?tab=add">Add your first expense</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($activeTab === 'add'): ?>
                <!-- Add Expense Tab: Form to add a new expense -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-icon success"><i class="fa-solid fa-plus"></i></div>
                        <div>
                            <div class="card-title">Add New Expense</div>
                            <div class="card-subtitle">Record a new expense to track your spending</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_expense">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="title" class="form-label">Expense Title *</label>
                                    <input type="text" id="title" name="title" class="form-input" 
                                           placeholder="e.g., Lunch at restaurant" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="amount" class="form-label">Amount ($) *</label>
                                    <input type="number" id="amount" name="amount" class="form-input" 
                                           placeholder="0.00" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="category" class="form-label">Category *</label>
                                    <select id="category" name="category" class="form-select" required>
                                        <option value="">Select a category</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category); ?>">
                                                <?php echo htmlspecialchars($category); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="expense_date" class="form-label">Date *</label>
                                    <input type="date" id="expense_date" name="expense_date" class="form-input" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea id="description" name="description" class="form-textarea" 
                                          placeholder="Add any additional notes about this expense..."></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Add Expense
                            </button>
                        </form>
                    </div>
                </div>

            <?php elseif ($activeTab === 'expenses'): ?>
                <!-- Expenses Tab: List and filter all your expenses -->
                <div class="filters">
                    <form id="filterForm" method="GET" action="">
                        <input type="hidden" name="tab" value="expenses">
                        <div class="filters-grid">
                            <div class="form-group">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-input" 
                                       placeholder="Search expenses..." 
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category); ?>" 
                                                <?php echo (($_GET['category'] ?? '') === $category) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Month</label>
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo $i; ?>" 
                                                <?php echo (($_GET['month'] ?? '') == $i) ? 'selected' : ''; ?>>
                                            <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Year</label>
                                <select name="year" class="form-select">
                                    <option value="">All Years</option>
                                    <?php for ($year = date('Y'); $year >= date('Y') - 5; $year--): ?>
                                        <option value="<?php echo $year; ?>" 
                                                <?php echo (($_GET['year'] ?? '') == $year) ? 'selected' : ''; ?>>
                                            <?php echo $year; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group" style="display: flex; align-items: end; gap: 10px;">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="?tab=expenses"><button type="button" class="btn btn-outline">Clear</button></a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-icon blue"><i class="fa-regular fa-clipboard"></i></div>
                        <div>
                            <div class="card-title">My Expenses</div>
                            <div class="card-subtitle">
                                Showing <?php echo count($paginatedExpenses); ?> of <?php echo $totalExpenses; ?> expenses
                                <?php if (array_sum(array_column($expenses, 'amount')) > 0): ?>
                                    • Total: $<?php echo number_format(array_sum(array_column($expenses, 'amount')), 2); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (count($paginatedExpenses) > 0): ?>
                            <div class="table-container">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Amount</th>
                                            <th>Category</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($paginatedExpenses as $expense): ?>
                                            <tr data-expense-id="<?php echo $expense['id']; ?>">
                                                <td><?php echo htmlspecialchars($expense['title']); ?></td>
                                                <td>$<?php echo number_format($expense['amount'], 2); ?></td>
                                                <td><span class="category-badge"><?php echo htmlspecialchars($expense['category']); ?></span></td>
                                                <td><?php echo date('Y-m-d', strtotime($expense['expense_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($expense['description'] ?: '-'); ?></td>
                                                <td>
                                                    <div class="actions">
                                                        <button class="btn btn-sm btn-secondary edit-expense" 
                                                                data-id="<?php echo $expense['id']; ?>">
                                                            <i class="fa-solid fa-pen"></i> Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger delete-expense" 
                                                                data-id="<?php echo $expense['id']; ?>"
                                                                data-title="<?php echo htmlspecialchars($expense['title']); ?>">
                                                            <i class="fa-solid fa-trash-can"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($totalPages > 1): ?>
                                <div class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="?tab=expenses&page=<?php echo $page - 1; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) { return $key !== 'tab' && $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                            Previous
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <?php if ($i === $page): ?>
                                            <span class="current"><?php echo $i; ?></span>
                                        <?php else: ?>
                                            <a href="?tab=expenses&page=<?php echo $i; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) { return $key !== 'tab' && $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="?tab=expenses&page=<?php echo $page + 1; ?>&<?php echo http_build_query(array_filter($_GET, function ($key) { return $key !== 'tab' && $key !== 'page'; }, ARRAY_FILTER_USE_KEY)); ?>">
                                            Next
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p style="text-align: center; color: var(--sec-gray-500); padding: 40px;">
                                No expenses found. <a href="?tab=add">Add your first expense</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif ($activeTab === 'reports'): ?>
                <!-- Reports Tab: See your stats and charts -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Total All Time</h3>
                            <div class="amount">$<?php echo number_format($totalAllTime, 2); ?></div>
                        </div>
                        <div class="stat-icon dark"><i class="fa-solid fa-sack-dollar" style="color: var(--primary-dark);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>This Year</h3>
                            <div class="amount">$<?php echo number_format($totalThisYear, 2); ?></div>
                        </div>
                        <div class="stat-icon blue"><i class="fa-solid fa-calendar" style="color: var(--primary-blue);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Categories Used</h3>
                            <div class="amount"><?php echo count($categorySummary); ?></div>
                        </div>
                        <div class="stat-icon orange"><i class="fa-solid fa-chart-simple"  style="color: var(--primary-orange);"></i></div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3>Average/Month</h3>
                            <div class="amount">$<?php echo count($monthlySummary) > 0 ? number_format($totalThisYear / max(1, count($monthlySummary)), 2) : '0.00'; ?></div>
                        </div>
                        <div class="stat-icon success"><i class="fa-solid fa-arrow-trend-up" style="color: var(--success);"></i></div>
                    </div>
                </div>

                <div class="category-grid">
                    <!-- Category Breakdown: See how much you spent in each category -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon orange"><i class="fa-solid fa-layer-group"></i></div>
                            <div>
                                <div class="card-title">Spending by Category</div>
                                <div class="card-subtitle">Current year breakdown</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (count($categorySummary) > 0): ?>
                                <canvas id="categoryChart" data-chart-data='<?php echo json_encode($categorySummary); ?>'></canvas>
                                <div style="margin-top: 20px;">
                                    <?php foreach ($categorySummary as $category): ?>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                            <strong><?php echo htmlspecialchars($category['category']); ?></strong>
                                            <strong>$<?php echo number_format($category['total'], 2); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="text-align: center; color: var(--sec-gray-500); padding: 40px;">
                                    No category data available
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Monthly Trends: See your spending pattern over the year -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon blue"><i class="fa-solid fa-arrow-trend-up"></i></div>
                            <div>
                                <div class="card-title">Monthly Trends</div>
                                <div class="card-subtitle">Spending patterns this year</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (count($monthlySummary) > 0): ?>
                                <canvas id="monthlyChart" data-chart-data='<?php echo json_encode($monthlySummary); ?>'></canvas>
                                <div style="margin-top: 20px;">
                                    <?php foreach (array_reverse($monthlySummary) as $month): ?>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                            <strong><?php echo htmlspecialchars($month['month_name']); ?></strong>
                                            <strong>$<?php echo number_format($month['total'], 2); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="text-align: center; color: var(--sec-gray-500); padding: 40px;">
                                    No monthly data available
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>


    <script src="assets/script.js"></script>
</body>
</html>