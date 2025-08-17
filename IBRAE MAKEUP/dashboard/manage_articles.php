<?php
/**
 * Manage Articles Page - Author Only
 * 
 * Allows Authors to manage their own articles
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/Article.php';

// Require Author access
SessionManager::requireUserType('Author');

$pageTitle = 'Manage My Articles';
$showNavigation = true;

$article = new Article();
$userId = SessionManager::getCurrentUserId();

$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // Create new article
        $title = sanitizeInput($_POST['title'] ?? '');
        $content = $_POST['content'] ?? ''; // Don't sanitize HTML content
        $display = $_POST['display'] ?? 'no';
        $order = intval($_POST['order'] ?? 0);
        
        // Validation
        $errors = [];
        
        if (empty($title)) $errors[] = 'Article title is required.';
        if (empty($content)) $errors[] = 'Article content is required.';
        if (!in_array($display, ['yes', 'no'])) $errors[] = 'Invalid display setting.';
        
        if (empty($errors)) {
            $newArticle = new Article();
            $newArticle->setAuthorId($userId);
            $newArticle->setArticleTitle($title);
            $newArticle->setArticleFullText($content);
            $newArticle->setArticleDisplay($display);
            $newArticle->setArticleOrder($order);
            
            if ($newArticle->create()) {
                $success = 'Article created successfully!';
                logActivity("New article created: $title");
            } else {
                $error = 'Failed to create article.';
            }
        } else {
            $error = implode('<br>', $errors);
        }
    } elseif ($action === 'delete') {
        $articleId = intval($_POST['article_id'] ?? 0);
        if ($articleId) {
            $articleData = $article->getById($articleId);
            if ($articleData && $articleData['authorId'] == $userId) {
                if ($article->delete($articleId)) {
                    $success = 'Article deleted successfully!';
                    logActivity("Article deleted: " . $articleData['article_title']);
                } else {
                    $error = 'Failed to delete article.';
                }
            } else {
                $error = 'Article not found or access denied.';
            }
        }
    }
}

// Get user's articles
$myArticles = $article->getByAuthor($userId);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-newspaper me-2"></i>
            Manage My Articles
        </h1>
        
        <?php echo generateBreadcrumb([
            ['text' => 'Dashboard', 'url' => 'dashboard.php'],
            ['text' => 'Manage Articles', 'url' => '#']
        ]); ?>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<!-- Add New Article Button -->
<div class="row mb-4">
    <div class="col-12">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addArticleModal">
            <i class="fas fa-plus me-1"></i>Add New Article
        </button>
    </div>
</div>

<!-- Articles List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    My Articles (<?php echo count($myArticles); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($myArticles)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Articles Yet</h5>
                        <p class="text-muted mb-3">You haven't created any articles yet. Click the button above to create your first article.</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                            <i class="fas fa-plus me-1"></i>Create First Article
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($myArticles as $articleData): ?>
                                    <tr class="searchable-row">
                                        <td>
                                            <strong><?php echo htmlspecialchars($articleData['article_title']); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo truncateText(strip_tags($articleData['article_full_text']), 80); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($articleData['article_display'] === 'yes'): ?>
                                                <span class="badge bg-success status-published">
                                                    <i class="fas fa-eye me-1"></i>Published
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning status-draft text-dark">
                                                    <i class="fas fa-edit me-1"></i>Draft
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $articleData['article_order']; ?></td>
                                        <td><?php echo formatDate($articleData['article_created_date']); ?></td>
                                        <td><?php echo formatDate($articleData['article_last_update']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewArticle(<?php echo $articleData['article_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editArticle(<?php echo $articleData['article_id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteArticle(<?php echo $articleData['article_id']; ?>, '<?php echo htmlspecialchars($articleData['article_title']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Article
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Article Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Article Content *</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                        <small class="text-muted">You can use HTML tags for formatting.</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="display" class="form-label">Status</label>
                            <select class="form-control" id="display" name="display">
                                <option value="no">Draft (Not Published)</option>
                                <option value="yes">Published</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="order" name="order" value="0" min="0">
                            <small class="text-muted">Higher numbers appear first</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i>Create Article
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Article Form (Hidden) -->
<form id="deleteArticleForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="article_id" id="deleteArticleId">
</form>

<script>
function viewArticle(articleId) {
    window.open('get_article.php?id=' + articleId, '_blank');
}

function editArticle(articleId) {
    // Redirect to edit page (we'll create this later)
    window.location.href = 'edit_article.php?id=' + articleId;
}

function deleteArticle(articleId, articleTitle) {
    if (confirm('Are you sure you want to delete the article "' + articleTitle + '"? This action cannot be undone.')) {
        document.getElementById('deleteArticleId').value = articleId;
        document.getElementById('deleteArticleForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
