<?php
/**
 * Article Model Class
 * 
 * Handles all article-related database operations
 */

// Get the correct path to connection.php
$connection_path = dirname(__DIR__) . '/config/connection.php';
if (!file_exists($connection_path)) {
    $connection_path = __DIR__ . '/../config/connection.php';
}
require_once $connection_path;

class Article {
    private $db;
    private $articleId;
    private $authorId;
    private $articleTitle;
    private $articleFullText;
    private $articleCreatedDate;
    private $articleLastUpdate;
    private $articleDisplay;
    private $articleOrder;
    
    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }
    
    // Getters
    public function getArticleId() { return $this->articleId; }
    public function getAuthorId() { return $this->authorId; }
    public function getArticleTitle() { return $this->articleTitle; }
    public function getArticleFullText() { return $this->articleFullText; }
    public function getArticleCreatedDate() { return $this->articleCreatedDate; }
    public function getArticleLastUpdate() { return $this->articleLastUpdate; }
    public function getArticleDisplay() { return $this->articleDisplay; }
    public function getArticleOrder() { return $this->articleOrder; }
    
    // Setters
    public function setArticleId($articleId) { $this->articleId = $articleId; }
    public function setAuthorId($authorId) { $this->authorId = $authorId; }
    public function setArticleTitle($articleTitle) { $this->articleTitle = $articleTitle; }
    public function setArticleFullText($articleFullText) { $this->articleFullText = $articleFullText; }
    public function setArticleDisplay($articleDisplay) { $this->articleDisplay = $articleDisplay; }
    public function setArticleOrder($articleOrder) { $this->articleOrder = $articleOrder; }
    
    /**
     * Create new article
     */
    public function create() {
        $stmt = $this->db->prepare("INSERT INTO articles (authorId, article_title, article_full_text, article_display, article_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $this->authorId, $this->articleTitle, $this->articleFullText, $this->articleDisplay, $this->articleOrder);
        
        if ($stmt->execute()) {
            $this->articleId = $this->db->getLastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Update article
     */
    public function update() {
        $stmt = $this->db->prepare("UPDATE articles SET article_title = ?, article_full_text = ?, article_display = ?, article_order = ? WHERE article_id = ?");
        $stmt->bind_param("sssii", $this->articleTitle, $this->articleFullText, $this->articleDisplay, $this->articleOrder, $this->articleId);
        
        return $stmt->execute();
    }
    
    /**
     * Delete article
     */
    public function delete($articleId) {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE article_id = ?");
        $stmt->bind_param("i", $articleId);
        return $stmt->execute();
    }
    
    /**
     * Get article by ID
     */
    public function getById($articleId) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.Full_Name as author_name 
            FROM articles a 
            JOIN users u ON a.authorId = u.userId 
            WHERE a.article_id = ?
        ");
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Get articles by author
     */
    public function getByAuthor($authorId) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.Full_Name as author_name 
            FROM articles a 
            JOIN users u ON a.authorId = u.userId 
            WHERE a.authorId = ? 
            ORDER BY a.article_created_date DESC
        ");
        $stmt->bind_param("i", $authorId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get latest articles (last 6)
     */
    public function getLatestArticles($limit = 6) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.Full_Name as author_name 
            FROM articles a 
            JOIN users u ON a.authorId = u.userId 
            WHERE a.article_display = 'yes' 
            ORDER BY a.article_created_date DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get all articles
     */
    public function getAll() {
        $stmt = $this->db->prepare("
            SELECT a.*, u.Full_Name as author_name 
            FROM articles a 
            JOIN users u ON a.authorId = u.userId 
            ORDER BY a.article_created_date DESC
        ");
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Search articles
     */
    public function search($searchTerm) {
        $searchTerm = '%' . $searchTerm . '%';
        $stmt = $this->db->prepare("
            SELECT a.*, u.Full_Name as author_name 
            FROM articles a 
            JOIN users u ON a.authorId = u.userId 
            WHERE (a.article_title LIKE ? OR a.article_full_text LIKE ?) 
            AND a.article_display = 'yes'
            ORDER BY a.article_created_date DESC
        ");
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get article statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total articles
        $result = $this->db->query("SELECT COUNT(*) as total FROM articles");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        // Published articles
        $result = $this->db->query("SELECT COUNT(*) as published FROM articles WHERE article_display = 'yes'");
        $stats['published'] = $result->fetch_assoc()['published'];
        
        // Draft articles
        $result = $this->db->query("SELECT COUNT(*) as drafts FROM articles WHERE article_display = 'no'");
        $stats['drafts'] = $result->fetch_assoc()['drafts'];
        
        // Articles by author
        $result = $this->db->query("
            SELECT u.Full_Name as author_name, COUNT(a.article_id) as article_count 
            FROM users u 
            LEFT JOIN articles a ON u.userId = a.authorId 
            WHERE u.UserType = 'Author' AND u.is_active = 1
            GROUP BY u.userId, u.Full_Name
            ORDER BY article_count DESC
        ");
        $stats['by_author'] = $result->fetch_all(MYSQLI_ASSOC);
        
        return $stats;
    }
}
?>
