<?php
/**
 * User Model Class
 * 
 * Handles all user-related database operations
 */

// Get the correct path to connection.php
$connection_path = dirname(__DIR__) . '/config/connection.php';
if (!file_exists($connection_path)) {
    $connection_path = __DIR__ . '/../config/connection.php';
}
require_once $connection_path;

class User {
    private $db;
    private $userId;
    private $fullName;
    private $email;
    private $phoneNumber;
    private $userName;
    private $password;
    private $userType;
    private $accessTime;
    private $profileImage;
    private $address;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    // Getters
    public function getUserId() { return $this->userId; }
    public function getFullName() { return $this->fullName; }
    public function getEmail() { return $this->email; }
    public function getPhoneNumber() { return $this->phoneNumber; }
    public function getUserName() { return $this->userName; }
    public function getUserType() { return $this->userType; }
    public function getAccessTime() { return $this->accessTime; }
    public function getProfileImage() { return $this->profileImage; }
    public function getAddress() { return $this->address; }
    
    // Setters
    public function setUserId($userId) { $this->userId = $userId; }
    public function setFullName($fullName) { $this->fullName = $fullName; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhoneNumber($phoneNumber) { $this->phoneNumber = $phoneNumber; }
    public function setUserName($userName) { $this->userName = $userName; }
    public function setPassword($password) { $this->password = password_hash($password, PASSWORD_DEFAULT); }
    public function setUserType($userType) { $this->userType = $userType; }
    public function setProfileImage($profileImage) { $this->profileImage = $profileImage; }
    public function setAddress($address) { $this->address = $address; }
    
    /**
     * Authenticate user login
     */
    public function authenticate($userName, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE User_Name = ? AND is_active = 1");
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['Password'])) {
                // Update access time
                $this->updateAccessTime($user['userId']);
                
                // Set user properties
                $this->userId = $user['userId'];
                $this->fullName = $user['Full_Name'];
                $this->email = $user['email'];
                $this->phoneNumber = $user['phone_Number'];
                $this->userName = $user['User_Name'];
                $this->userType = $user['UserType'];
                $this->accessTime = $user['AccessTime'];
                $this->profileImage = $user['profile_Image'];
                $this->address = $user['Address'];
                
                return true;
            }
        }
        return false;
    }
    
    /**
     * Create new user
     */
    public function create() {
        $stmt = $this->db->prepare("INSERT INTO users (Full_Name, email, phone_Number, User_Name, Password, UserType, profile_Image, Address) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $this->fullName, $this->email, $this->phoneNumber, $this->userName, $this->password, $this->userType, $this->profileImage, $this->address);
        
        if ($stmt->execute()) {
            $this->userId = $this->db->getLastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Update user
     */
    public function update() {
        if ($this->password) {
            $stmt = $this->db->prepare("UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, Password = ?, profile_Image = ?, Address = ? WHERE userId = ?");
            $stmt->bind_param("ssssssi", $this->fullName, $this->email, $this->phoneNumber, $this->password, $this->profileImage, $this->address, $this->userId);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, profile_Image = ?, Address = ? WHERE userId = ?");
            $stmt->bind_param("sssssi", $this->fullName, $this->email, $this->phoneNumber, $this->profileImage, $this->address, $this->userId);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Delete user
     */
    public function delete($userId) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = 0 WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
    
    /**
     * Get user by ID
     */
    public function getById($userId) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE userId = ? AND is_active = 1");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Get all users by type
     */
    public function getAllByType($userType, $excludeUserId = null) {
        $sql = "SELECT * FROM users WHERE UserType = ? AND is_active = 1";
        $params = [$userType];
        $types = "s";
        
        if ($excludeUserId) {
            $sql .= " AND userId != ?";
            $params[] = $excludeUserId;
            $types .= "i";
        }
        
        $sql .= " ORDER BY Full_Name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($userName, $excludeUserId = null) {
        $sql = "SELECT userId FROM users WHERE User_Name = ? AND is_active = 1";
        $params = [$userName];
        $types = "s";
        
        if ($excludeUserId) {
            $sql .= " AND userId != ?";
            $params[] = $excludeUserId;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeUserId = null) {
        $sql = "SELECT userId FROM users WHERE email = ? AND is_active = 1";
        $params = [$email];
        $types = "s";
        
        if ($excludeUserId) {
            $sql .= " AND userId != ?";
            $params[] = $excludeUserId;
            $types .= "i";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Update access time
     */
    private function updateAccessTime($userId) {
        $stmt = $this->db->prepare("UPDATE users SET AccessTime = CURRENT_TIMESTAMP WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
    }
}
?>
