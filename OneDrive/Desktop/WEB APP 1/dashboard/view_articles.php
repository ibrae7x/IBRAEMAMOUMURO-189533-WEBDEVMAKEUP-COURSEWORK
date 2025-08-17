<?php
/**
 * View Articles Page
 * 
 * Shows the latest 6 articles in descending order by creation date
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/Article.php';

// Require login
SessionManager::requireLogin();

$pageTitle = 'View Articles';
$showNavigation = true;

$article = new Article();
$articles = $article->getLatestArticles(6);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-eye me-2"></i>
            Latest Articles
        </h1>
        
        <?php echo generateBreadcrumb([
            ['text' => 'Dashboard', 'url' => 'dashboard.php'],
            ['text' => 'View Articles', 'url' => '#']
        ]); ?>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input 
                type="text" 
                class="form-control" 
                id="search-input" 
                placeholder="Search articles..."
                data-target=".article-card"
            >
        </div>
    </div>
    <div class="col-md-6 text-md-end">
        <span class="text-muted" id="search-results-count">
            Showing <?php echo count($articles); ?> articles
        </span>
    </div>
</div>

<?php if (empty($articles)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Articles Found</h5>
                    <p class="text-muted mb-0">There are no published articles to display at this time.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($articles as $articleData): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 article-card">
                    <div class="card-body">
                        <h5 class="card-title article-title">
                            <?php echo htmlspecialchars($articleData['article_title']); ?>
                        </h5>
                        
                        <div class="article-meta mb-3">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>
                                By <?php echo htmlspecialchars($articleData['author_name']); ?>
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo formatDate($articleData['article_created_date'], 'M d, Y'); ?>
                            </small>
                            <?php if ($articleData['article_last_update'] !== $articleData['article_created_date']): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="fas fa-edit me-1"></i>
                                    Updated <?php echo formatDate($articleData['article_last_update'], 'M d, Y'); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="article-excerpt">
                            <?php echo truncateText(strip_tags($articleData['article_full_text']), 150); ?>
                        </div>
                        
                        <div class="mt-3">
                            <span class="badge bg-success status-published">
                                <i class="fas fa-check me-1"></i>Published
                            </span>
                            
                            <?php if ($articleData['article_order'] > 0): ?>
                                <span class="badge bg-info">
                                    Order: <?php echo $articleData['article_order']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <button 
                                type="button" 
                                class="btn btn-outline-primary btn-sm"
                                onclick="viewFullArticle(<?php echo $articleData['article_id']; ?>)"
                            >
                                <i class="fas fa-eye me-1"></i>Read More
                            </button>
                            
                            <?php if (SessionManager::getCurrentUserType() === 'Author' && $articleData['authorId'] == SessionManager::getCurrentUserId()): ?>
                                <a href="edit_article.php?id=<?php echo $articleData['article_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination (if needed) -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <p class="text-muted">
                Showing the latest <?php echo count($articles); ?> articles. 
                <?php if (count($articles) === 6): ?>
                    <a href="all_articles.php" class="text-decoration-none">View all articles</a>
                <?php endif; ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<!-- Full Article Modal -->
<div class="modal fade" id="fullArticleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="articleModalTitle">Article Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="articleModalMeta" class="article-meta mb-3"></div>
                <div id="articleModalContent" class="article-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewFullArticle(articleId) {
    // You can implement AJAX call here to get full article content
    // For now, we'll show a simple implementation
    
    fetch('get_article.php?id=' + articleId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('articleModalTitle').textContent = data.article.article_title;
                document.getElementById('articleModalMeta').innerHTML = 
                    '<small class="text-muted">' +
                    '<i class="fas fa-user me-1"></i>By ' + data.article.author_name + ' • ' +
                    '<i class="fas fa-calendar me-1"></i>' + data.article.formatted_date +
                    '</small>';
                document.getElementById('articleModalContent').innerHTML = data.article.article_full_text;
                
                var modal = new bootstrap.Modal(document.getElementById('fullArticleModal'));
                modal.show();
            } else {
                alert('Failed to load article content.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the article.');
        });
}

// Initialize search functionality
$(document).ready(function() {
    $('#search-input').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        $('.article-card').each(function() {
            const articleText = $(this).text().toLowerCase();
            
            if (articleText.includes(searchTerm)) {
                $(this).closest('.col-lg-6').show();
            } else {
                $(this).closest('.col-lg-6').hide();
            }
        });
        
        // Update results counter
        const visibleCards = $('.article-card:visible').length;
        $('#search-results-count').text('Showing ' + visibleCards + ' articles');
    });
});
</script>

<?php include '../includes/footer.php'; ?>
