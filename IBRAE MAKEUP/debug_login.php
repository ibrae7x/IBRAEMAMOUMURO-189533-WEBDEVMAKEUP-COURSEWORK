<?php
/**
 * Debug Login Page
 * 
 * This page helps debug login issues
 */

require_once 'config/constants.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';
require_once 'models/User.php';

echo "<h2>Debug Login Information</h2>";

// Test database connection
try {
    $db = DatabaseConnection::getInstance();
    echo "✅ Database connection: SUCCESS<br>";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED - " . $e->getMessage() . "<br>";
    exit();
}

// Check if users table exists and get users
$result = $db->query("SELECT User_Name, Password, UserType FROM users WHERE User_Name = 'superadmin'");
if ($result && $result->num_rows > 0) {
    echo "✅ Users table exists and superadmin found<br>";
    $user = $result->fetch_assoc();
    echo "Username: " . $user['User_Name'] . "<br>";
    echo "UserType: " . $user['UserType'] . "<br>";
    echo "Password hash: " . substr($user['Password'], 0, 20) . "...<br>";
    
    // Test password verification
    $testPassword = 'admin123';
    if (password_verify($testPassword, $user['Password'])) {
        echo "✅ Password verification: SUCCESS<br>";
    } else {
        echo "❌ Password verification: FAILED<br>";
        
        // Try to update with a new hash
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        $updateSql = "UPDATE users SET Password = '$newHash' WHERE User_Name = 'superadmin'";
        if ($db->query($updateSql)) {
            echo "✅ Password updated with new hash<br>";
        } else {
            echo "❌ Failed to update password<br>";
        }
    }
} else {
    echo "❌ superadmin user not found in database<br>";
    echo "Creating superadmin user...<br>";
    
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (Full_Name, email, phone_Number, User_Name, Password, UserType, Address) 
            VALUES ('Super Administrator', 'admin@example.com', '+1234567890', 'superadmin', '$hashedPassword', 'Super_User', '123 Admin Street, Admin City')";
    
    if ($db->query($sql)) {
        echo "✅ superadmin user created successfully<br>";
    } else {
        echo "❌ Failed to create superadmin user: " . $db->getConnection()->error . "<br>";
    }
}

// Test login process
echo "<br><h3>Test Login Process</h3>";
$user = new User();
if ($user->authenticate('superadmin', 'admin123')) {
    echo "✅ Authentication: SUCCESS<br>";
    echo "User ID: " . $user->getUserId() . "<br>";
    echo "Full Name: " . $user->getFullName() . "<br>";
    echo "User Type: " . $user->getUserType() . "<br>";
} else {
    echo "❌ Authentication: FAILED<br>";
}

echo "<br><a href='index.php'>Back to Login</a>";
?>
