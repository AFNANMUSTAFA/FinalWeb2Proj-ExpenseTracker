// JavaScript for the expense tracker app

// When the page loads, start the app
document.addEventListener('DOMContentLoaded', function () {
    initializeApp();
});

function initializeApp() {
    // Set up form checks
    initializeFormValidation();

    // Set up table buttons
    initializeTableActions();

    // Set up filters
    initializeFilters();

    // If we're on the reports page, show the charts
    if (document.getElementById('categoryChart')) {
        initializeCharts();
    }
}

// Check forms before sending
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    // Check if email looks right
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        }
    });

    // Check if passwords match
    const passwordField = form.querySelector('input[name="password"]');
    const confirmPasswordField = form.querySelector('input[name="confirm_password"]');

    if (passwordField && confirmPasswordField) {
        if (passwordField.value !== confirmPasswordField.value) {
            showFieldError(confirmPasswordField, 'Passwords do not match');
            isValid = false;
        }
    }

    // Check if amount is more than 0
    const amountFields = form.querySelectorAll('input[type="number"]');
    amountFields.forEach(field => {
        if (field.value && parseFloat(field.value) <= 0) {
            showFieldError(field, 'Amount must be greater than 0');
            isValid = false;
        }
    });

    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#ef4444';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;

    field.style.borderColor = '#ef4444';
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    field.style.borderColor = '';
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Set up edit and delete buttons for expenses
function initializeTableActions() {
    // Edit button
    const editButtons = document.querySelectorAll('.edit-expense');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const expenseId = this.dataset.id;
            editExpense(expenseId);
        });
    });

    // Delete button
    const deleteButtons = document.querySelectorAll('.delete-expense');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const expenseId = this.dataset.id;
            const expenseTitle = this.dataset.title;
            deleteExpense(expenseId, expenseTitle);
        });
    });
}

function editExpense(expenseId) {
    // Get info from the table row
    const row = document.querySelector(`tr[data-expense-id="${expenseId}"]`);
    if (!row) return;

    const cells = row.querySelectorAll('td');
    const title = cells[0].textContent.trim();
    const amount = cells[1].textContent.replace('$', '').trim();
    const category = cells[2].textContent.trim();
    const date = cells[3].textContent.trim();
    const description = cells[4].textContent.trim();

    // Show the edit form in a popup
    const modal = createEditModal(expenseId, title, amount, category, date, description);
    document.body.appendChild(modal);
    modal.style.display = 'flex';
}

function createEditModal(expenseId, title, amount, category, date, description) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    `;

    const categories = ['Food & Dining', 'Transportation', 'Shopping', 'Entertainment', 'Bills & Utilities', 'Healthcare', 'Education', 'Travel', 'Other'];

    modal.innerHTML = `
        <div class="modal-content" style="background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px;">
            <h3 style="margin-bottom: 20px; color: var(--sec-gray-900);">Edit Expense</h3>
            <form id="editExpenseForm" method="POST" action="dashboard.php">
                <input type="hidden" name="action" value="update_expense">
                <input type="hidden" name="expense_id" value="${expenseId}">
                
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-input" value="${title}" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Amount ($)</label>
                        <input type="number" name="amount" class="form-input" value="${amount}" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            ${categories.map(cat => `<option value="${cat}" ${cat === category ? 'selected' : ''}>${cat}</option>`).join('')}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="date" name="expense_date" class="form-input" value="${date}" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea">${description}</textarea>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="btn btn-outline" onclick="closeModal(this)">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Expense</button>
                </div>
            </form>
        </div>
    `;

    // Close the popup if you click outside it
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal(modal.querySelector('.btn-outline'));
        }
    });

    return modal;
}

function closeModal(button) {
    const modal = button.closest('.modal');
    if (modal) {
        modal.remove();
    }
}

function deleteExpense(expenseId, expenseTitle) {
    if (confirm(`Are you sure you want to delete "${expenseTitle}"?`)) {
        // Make a form to delete the expense
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'dashboard.php';
        form.style.display = 'none';

        form.innerHTML = `
            <input type="hidden" name="action" value="delete_expense">
            <input type="hidden" name="expense_id" value="${expenseId}">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}

// Set up filters so the list updates when you change something
function initializeFilters() {
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        const filterInputs = filterForm.querySelectorAll('input, select');

        filterInputs.forEach(input => {
            input.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    }
}
 
// Show charts if we're on the reports page
function initializeCharts() {
    // Category chart
    const categoryChartCanvas = document.getElementById('categoryChart');
    if (categoryChartCanvas && typeof Chart !== 'undefined') {
        const categoryData = JSON.parse(categoryChartCanvas.dataset.chartData || '[]');

        new Chart(categoryChartCanvas, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.category),
                datasets: [{
                    data: categoryData.map(item => item.total),
                    backgroundColor: [
                        '#F48C06',
                        '#65DAFF',
                        '#2F327D',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4',
                        '#84cc16'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Monthly chart
    const monthlyChartCanvas = document.getElementById('monthlyChart');
    if (monthlyChartCanvas && typeof Chart !== 'undefined') {
        const monthlyData = JSON.parse(monthlyChartCanvas.dataset.chartData || '[]');

        new Chart(monthlyChartCanvas, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month_name),
                datasets: [{
                    label: 'Monthly Expenses',
                    data: monthlyData.map(item => item.total),
                    borderColor: '#F48C06',
                    backgroundColor: 'rgba(244, 140, 6, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return '$' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }
}

// Format a number as money
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format a date nicely
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Show a loading spinner on a button
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="loading"></span> Loading...';
    element.disabled = true;

    return function hideLoading() {
        element.innerHTML = originalContent;
        element.disabled = false;
    };
}

// Show a message popup
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add some simple animations for popups
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);