<?php
/**
 * Logout Page
 * 
 * Handles user logout and session cleanup
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Log the logout activity if user is logged in
if (SessionManager::isLoggedIn()) {
    $username = SessionManager::getCurrentUserName();
    logActivity("User logged out: " . $username);
}

// Logout user
SessionManager::logout();

// Redirect to index page with success message
header('Location: ../index.php?message=logged_out');
exit();
?>
