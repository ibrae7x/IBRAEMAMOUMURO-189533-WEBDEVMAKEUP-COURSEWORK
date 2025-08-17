<?php
/**
 * Database Connection Class
 * 
 * This class handles the database connection using MySQLi Object-Oriented approach
 */

// Get the correct path to constants.php
$constants_path = __DIR__ . '/constants.php';
require_once $constants_path;

class DatabaseConnection {
    private $connection;
    private static $instance = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get single instance of database connection (Singleton pattern)
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     */
    private function connect() {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset to utf8
            $this->connection->set_charset("utf8");
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please contact administrator.");
        }
    }
    
    /**
     * Get the database connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prepare statement
     */
    public function prepare($query) {
        return $this->connection->prepare($query);
    }
    
    /**
     * Execute query
     */
    public function query($query) {
        return $this->connection->query($query);
    }
    
    /**
     * Get last insert ID
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Escape string
     */
    public function escapeString($string) {
        return $this->connection->real_escape_string($string);
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->autocommit(false);
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
