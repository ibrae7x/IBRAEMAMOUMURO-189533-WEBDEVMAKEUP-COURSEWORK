<?php
/**
 * Session Management Class
 * 
 * Handles user sessions and authentication
 */

// Get the correct path to constants.php
$constants_path = dirname(__DIR__) . '/config/constants.php';
if (!file_exists($constants_path)) {
    $constants_path = __DIR__ . '/../config/constants.php';
}
require_once $constants_path;

class SessionManager {
    
    public static function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        self::startSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
    }
    
    /**
     * Login user
     */
    public static function login($user) {
        self::startSession();
        $_SESSION['user_id'] = $user->getUserId();
        $_SESSION['user_name'] = $user->getUserName();
        $_SESSION['full_name'] = $user->getFullName();
        $_SESSION['user_type'] = $user->getUserType();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::startSession();
        session_unset();
        session_destroy();
    }
    
    /**
     * Check session timeout
     */
    public static function checkTimeout() {
        self::startSession();
        
        if (self::isLoggedIn()) {
            $last_activity = $_SESSION['last_activity'] ?? 0;
            
            if ((time() - $last_activity) > SESSION_TIMEOUT) {
                self::logout();
                return false;
            }
            
            $_SESSION['last_activity'] = time();
        }
        
        return self::isLoggedIn();
    }
    
    /**
     * Get current user ID
     */
    public static function getCurrentUserId() {
        self::startSession();
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user type
     */
    public static function getCurrentUserType() {
        self::startSession();
        return $_SESSION['user_type'] ?? null;
    }
    
    /**
     * Get current user name
     */
    public static function getCurrentUserName() {
        self::startSession();
        return $_SESSION['user_name'] ?? null;
    }
    
    /**
     * Get current full name
     */
    public static function getCurrentFullName() {
        self::startSession();
        return $_SESSION['full_name'] ?? null;
    }
    
    /**
     * Check if user has permission
     */
    public static function hasPermission($requiredUserType) {
        $currentUserType = self::getCurrentUserType();
        
        if (!$currentUserType) {
            return false;
        }
        
        // Define permission hierarchy
        $permissions = [
            'Super_User' => ['Super_User', 'Administrator', 'Author'],
            'Administrator' => ['Administrator', 'Author'],
            'Author' => ['Author']
        ];
        
        return in_array($requiredUserType, $permissions[$currentUserType] ?? []);
    }
    
    /**
     * Require login
     */
    public static function requireLogin() {
        if (!self::checkTimeout()) {
            header('Location: ../index.php?error=session_expired');
            exit();
        }
    }
    
    /**
     * Require specific user type
     */
    public static function requireUserType($requiredUserType) {
        self::requireLogin();
        
        if (!self::hasPermission($requiredUserType)) {
            header('Location: ../index.php?error=access_denied');
            exit();
        }
    }
    
    /**
     * Set flash message
     */
    public static function setFlashMessage($message, $type = 'info') {
        self::startSession();
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    /**
     * Get and clear flash message
     */
    public static function getFlashMessage() {
        self::startSession();
        
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info';
            
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            
            return ['message' => $message, 'type' => $type];
        }
        
        return null;
    }
}
?>
