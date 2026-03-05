# 🚀 XAMPP Setup Guide for Personal Expense Tracker

This guide will help you set up the Personal Expense Tracker application on XAMPP.

## <i class="fa-regular fa-clipboard"></i> Prerequisites

1. **Download and Install XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download the latest version for your operating system
   - Install XAMPP with default settings

## 🔧 Installation Steps

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service (click "Start" button)
3. Start **MySQL** service (click "Start" button)
4. Both should show "Running" status with green background

### Step 2: Place Project Files
1. Copy the entire project folder to XAMPP's `htdocs` directory:
   - **Windows**: `C:\xampp\htdocs\expense-tracker\`
   - **Mac**: `/Applications/XAMPP/htdocs/expense-tracker/`
   - **Linux**: `/opt/lampp/htdocs/expense-tracker/`

### Step 3: Check System Requirements
1. Open your web browser
2. Navigate to: `http://localhost/expense-tracker/setup/check_requirements.php`
3. Verify all requirements are met (should show green checkmarks)

### Step 4: Run Installation
1. If requirements check passes, click "Continue to Installation"
2. Or directly visit: `http://localhost/expense-tracker/setup/install.php`
3. Configure database settings:
   - **Database Host**: `localhost` (default)
   - **Database Username**: `root` (default)
   - **Database Password**: Leave empty (default for XAMPP)
   - **Database Name**: `expense_tracker` (or your preferred name)
4. Click "Install Application"

### Step 5: Access the Application
1. After successful installation, you'll be redirected to the login page
2. Or manually visit: `http://localhost/expense-tracker/`
3. Register a new account to start using the application

## 🗄️ Database Management

### Access phpMyAdmin
1. Open: `http://localhost/phpmyadmin/`
2. You can view and manage your database here
3. The application will create tables automatically

### Manual Database Setup (if needed)
If automatic setup fails, you can manually create the database:

1. Open phpMyAdmin
2. Click "New" to create a database
3. Name it `expense_tracker`
4. Set collation to `utf8mb4_unicode_ci`
5. Import the `database.sql` file from the project root

## 🔧 Troubleshooting

### Common Issues:

**1. "Connection failed" error:**
- Ensure MySQL service is running in XAMPP
- Check database credentials in `config/database.php`
- Verify database name exists

**2. "Page not found" error:**
- Ensure Apache service is running
- Check that files are in the correct `htdocs` directory
- Verify the URL: `http://localhost/expense-tracker/`

**3. Permission errors:**
- On Linux/Mac, you might need to set proper permissions:
  ```bash
  chmod -R 755 /opt/lampp/htdocs/expense-tracker/
  chmod -R 777 /opt/lampp/htdocs/expense-tracker/config/
  ```

**4. PHP errors:**
- Check XAMPP PHP version (should be 7.4+)
- Enable error reporting by adding to `config/database.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

### Port Conflicts:
If port 80 (Apache) or 3306 (MySQL) are in use:

1. **Apache Port Change:**
   - Open XAMPP Control Panel
   - Click "Config" next to Apache
   - Select "httpd.conf"
   - Change `Listen 80` to `Listen 8080`
   - Access via: `http://localhost:8080/expense-tracker/`

2. **MySQL Port Change:**
   - Click "Config" next to MySQL
   - Select "my.ini"
   - Change `port=3306` to `port=3307`
   - Update `config/database.php` accordingly

## 🎯 Default Access

- **Application URL**: `http://localhost/expense-tracker/`
- **phpMyAdmin**: `http://localhost/phpmyadmin/`
- **XAMPP Dashboard**: `http://localhost/`

## 📱 Testing the Application

1. **Register a new user account**
2. **Add some sample expenses**
3. **Test filtering and search features**
4. **View reports and analytics**
5. **Try editing and deleting expenses**

## 🔒 Security Notes

For production deployment:
- Change default MySQL password
- Update database credentials
- Enable HTTPS
- Configure proper file permissions
- Remove setup files after installation

## 📞 Support

If you encounter issues:
1. Check XAMPP error logs in `xampp/apache/logs/`
2. Verify all services are running
3. Ensure PHP extensions are enabled
4. Check file permissions

---

**🎉 Congratulations!** Your Personal Expense Tracker is now ready to use with XAMPP!