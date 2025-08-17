<?php
/**
 * Database Setup Script
 * 
 * Run this script once to create the database and tables
 */

require_once '../config/constants.php';

// Create connection without selecting database first
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci";
if ($connection->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $connection->error . "<br>";
}

// Select the database
$connection->select_db(DB_NAME);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    Full_Name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_Number VARCHAR(20),
    User_Name VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    UserType ENUM('Super_User', 'Administrator', 'Author') NOT NULL,
    AccessTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    profile_Image VARCHAR(255) DEFAULT NULL,
    Address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
)";

if ($connection->query($sql) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating users table: " . $connection->error . "<br>";
}

// Create articles table
$sql = "CREATE TABLE IF NOT EXISTS articles (
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    authorId INT NOT NULL,
    article_title VARCHAR(255) NOT NULL,
    article_full_text TEXT NOT NULL,
    article_created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    article_last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    article_display ENUM('yes', 'no') DEFAULT 'yes',
    article_order INT DEFAULT 0,
    FOREIGN KEY (authorId) REFERENCES users(userId) ON DELETE CASCADE
)";

if ($connection->query($sql) === TRUE) {
    echo "Articles table created successfully.<br>";
} else {
    echo "Error creating articles table: " . $connection->error . "<br>";
}

// Insert default Super_User
$plainPassword = 'admin123';
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO users (Full_Name, email, phone_Number, User_Name, Password, UserType, Address) 
        VALUES ('Super Administrator', 'admin@example.com', '+1234567890', 'superadmin', '$hashedPassword', 'Super_User', '123 Admin Street, Admin City')";

if ($connection->query($sql) === TRUE) {
    echo "Default Super_User created successfully.<br>";
    echo "Username: superadmin<br>";
    echo "Password: admin123<br>";
    echo "Password hash: " . $hashedPassword . "<br>";
} else {
    echo "Error creating default user: " . $connection->error . "<br>";
}

// Create uploads directory for profile images
$uploadDir = '../uploads/profile_images/';
if (!file_exists($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "Upload directory created successfully.<br>";
    } else {
        echo "Error creating upload directory.<br>";
    }
}

$connection->close();
echo "<br>Database setup completed!<br>";
echo "<a href='../index.php'>Go to Application</a>";
?>
