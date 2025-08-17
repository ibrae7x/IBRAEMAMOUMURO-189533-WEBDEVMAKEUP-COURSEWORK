<?php
/**
 * Get Article API
 * 
 * Returns article data in JSON format
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/Article.php';

// Require login
SessionManager::requireLogin();

header('Content-Type: application/json');

$articleId = intval($_GET['id'] ?? 0);

if ($articleId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid article ID']);
    exit;
}

$article = new Article();
$articleData = $article->getById($articleId);

if (!$articleData) {
    echo json_encode(['success' => false, 'message' => 'Article not found']);
    exit;
}

// Check if article is published or if user has permission to view
$userType = SessionManager::getCurrentUserType();
$userId = SessionManager::getCurrentUserId();

if ($articleData['article_display'] === 'no' && 
    $userType === 'Author' && 
    $articleData['authorId'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

$response = [
    'success' => true,
    'article' => [
        'article_id' => $articleData['article_id'],
        'article_title' => $articleData['article_title'],
        'article_full_text' => $articleData['article_full_text'],
        'author_name' => $articleData['author_name'],
        'formatted_date' => formatDate($articleData['article_created_date'], 'M d, Y H:i'),
        'article_display' => $articleData['article_display'],
        'article_order' => $articleData['article_order']
    ]
];

echo json_encode($response);
?>
