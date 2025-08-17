<?php
/**
 * Database Configuration Constants
 * 
 * This file contains all the database connection constants
 * required for the application to connect to MySQL database.
 */

// Database connection constants
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'user_management_system');

// Application constants
define('SITE_URL', 'http://localhost/WEB_APP_1/');
define('APP_NAME', 'User Management System');

// User type constants
define('USER_TYPE_SUPER', 'Super_User');
define('USER_TYPE_ADMIN', 'Administrator');
define('USER_TYPE_AUTHOR', 'Author');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// File upload settings
define('MAX_FILE_SIZE', 2097152); // 2MB
define('UPLOAD_PATH', 'uploads/profile_images/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
?>
