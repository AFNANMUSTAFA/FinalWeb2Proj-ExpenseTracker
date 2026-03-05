# Personal Expense Tracker

A modern, responsive web-based expense tracking system built with pure PHP, HTML, CSS, and JavaScript. This application allows users to manage their personal finances by recording, categorizing, and analyzing their daily expenses.

## 🎨 Design Features

- **Modern UI**: Clean, professional design with smooth animations and micro-interactions
- **Color Palette**: Custom color scheme using #F48C06 (Orange), #65DAFF (Blue), and #2F327D (Dark Blue)
- **Responsive Design**: Fully responsive layout that works on desktop, tablet, and mobile devices
- **Interactive Elements**: Hover effects, transitions, and dynamic content updates

## 🚀 Features

### Authentication System
- Secure user registration and login
- Password hashing using PHP's `password_hash()` function
- Session management for user authentication
- Logout functionality

### Expense Management
- Add new expenses with title, amount, category, date, and description
- Edit existing expenses with inline editing
- Delete expenses with confirmation
- Categorize expenses (Food & Dining, Transportation, Shopping, etc.)
- Search and filter expenses by category, date, or keywords

### Reporting & Analytics
- Dashboard overview with key statistics
- Monthly and yearly expense summaries
- Category-wise spending breakdown
- Visual charts using Chart.js
- Pagination for large expense lists

### User Interface
- Clean, modern dashboard with tabbed navigation
- Real-time form validation
- Interactive modals for editing expenses
- Toast notifications for user feedback
- Loading states and smooth transitions

## 🛠️ Technologies Used

- **Backend**: Pure PHP (no frameworks)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Charts**: Chart.js for data visualization
- **Icons**: Font Awesome for lightweight icons

## 📁 Project Structure

```
expense-tracker/
├── config/
│   └── database.php          # Database configuration and initialization
├── includes/
│   └── functions.php         # Core PHP functions for auth and expenses
├── assets/
│   ├── style.css            # Main stylesheet with custom design
│   └── script.js            # JavaScript for interactivity
├── index.php                # Login page
├── register.php             # User registration page
├── dashboard.php            # Main application dashboard
├── logout.php               # Logout handler
└── README.md               # Project documentation
```

## 🗄️ Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Expenses Table
```sql
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    expense_date DATE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or local development environment (XAMPP, WAMP, MAMP)

### Installation Steps

1. **Clone or Download** the project files to your web server directory

2. **Database Setup**:
   - The application will automatically create the database and tables on first run
   - Default database name: `expense_tracker`
   - Update database credentials in `config/database.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'expense_tracker');
   ```

3. **Web Server Configuration**:
   - Ensure your web server has PHP and MySQL enabled
   - Place the project files in your web root directory
   - For XAMPP: Place in `htdocs/expense-tracker/`

4. **Access the Application**:
   - Open your web browser
   - Navigate to `http://localhost/expense-tracker/`
   - Register a new account or use existing credentials

### Local Development with XAMPP

1. Download and install XAMPP
2. Start Apache and MySQL services
3. Place project files in `C:\xampp\htdocs\expense-tracker\`
4. Access via `http://localhost/expense-tracker/`

## 📱 Usage Guide

### Getting Started
1. **Register**: Create a new account with username, email, and password
2. **Login**: Sign in with your credentials
3. **Dashboard**: View your expense overview and statistics

### Managing Expenses
1. **Add Expense**: Click "Add Expense" tab and fill in the details
2. **View Expenses**: Use "My Expenses" tab to see all your expenses
3. **Filter**: Use the filter options to find specific expenses
4. **Edit**: Click the "Edit" button on any expense to modify it
5. **Delete**: Click the "Delete" button to remove an expense

### Reports & Analytics
1. **Overview**: Dashboard shows key statistics and recent expenses
2. **Reports**: View detailed charts and category breakdowns
3. **Monthly Trends**: Analyze spending patterns over time

## 🔒 Security Features

- Password hashing using PHP's built-in functions
- SQL injection prevention using prepared statements
- Session-based authentication
- Input validation and sanitization
- CSRF protection through form tokens

## 🎨 Customization

### Color Scheme
The application uses CSS custom properties for easy color customization:
```css
:root {
    --primary-orange: #F48C06;
    --primary-blue: #65DAFF;
    --primary-dark: #2F327D;
}
```

### Adding New Categories
Edit the `getExpenseCategories()` function in `includes/functions.php`:
```php
function getExpenseCategories() {
    return [
        'Food & Dining',
        'Transportation',
        'Your New Category',
        // ... other categories
    ];
}
```

## 🚀 Future Enhancements

- **Export Features**: CSV/Excel export for expenses
- **Multi-currency Support**: Handle different currencies
- **Budget Tracking**: Set and monitor spending budgets
- **Recurring Expenses**: Automatic recurring expense entries
- **Mobile App**: Native mobile application
- **Email Notifications**: Spending alerts and summaries
- **Data Backup**: Automated backup functionality

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**:
   - Check MySQL service is running
   - Verify database credentials in `config/database.php`
   - Ensure database user has proper permissions

2. **Page Not Loading**:
   - Check Apache/web server is running
   - Verify file permissions
   - Check PHP error logs

3. **Charts Not Displaying**:
   - Ensure Chart.js is loading properly
   - Check browser console for JavaScript errors
   - Verify data is being passed to charts

### Error Logs
- Check PHP error logs in your server's log directory
- Enable error reporting for development:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

---

**Built with ❤️ By Eng. Salman Abualhin Using pure PHP, HTML, CSS, and JavaScript**