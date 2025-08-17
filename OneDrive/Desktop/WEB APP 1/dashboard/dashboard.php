<?php
/**
 * Dashboard - Main dashboard page for all user types
 * 
 * Shows different content based on user type
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/User.php';
require_once '../models/Article.php';

// Require login
SessionManager::requireLogin();

$pageTitle = 'Dashboard';
$showNavigation = true;

$userType = SessionManager::getCurrentUserType();
$userId = SessionManager::getCurrentUserId();
$fullName = SessionManager::getCurrentFullName();

// Get statistics based on user type
$user = new User();
$article = new Article();

$stats = [];

if ($userType === 'Super_User') {
    $stats['total_users'] = count($user->getAllByType('Administrator')) + count($user->getAllByType('Author'));
    $stats['administrators'] = count($user->getAllByType('Administrator'));
    $stats['authors'] = count($user->getAllByType('Author'));
} elseif ($userType === 'Administrator') {
    $stats['authors'] = count($user->getAllByType('Author'));
} elseif ($userType === 'Author') {
    $stats['my_articles'] = count($article->getByAuthor($userId));
}

$stats['total_articles'] = count($article->getAll());
$stats['latest_articles'] = $article->getLatestArticles(6);

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>
            Welcome back, <?php echo htmlspecialchars($fullName); ?>!
        </h1>
        
        <div class="alert alert-info" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            You are logged in as: <strong><?php echo getUserTypeDisplayName($userType); ?></strong>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <?php if ($userType === 'Super_User'): ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-primary text-white">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $stats['total_users']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-success text-white">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h5 class="card-title">Administrators</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $stats['administrators']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card dashboard-card bg-info text-white">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h5 class="card-title">Authors</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $stats['authors']; ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($userType === 'Administrator'): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card dashboard-card bg-info text-white">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h5 class="card-title">Authors</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $stats['authors']; ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($userType === 'Author'): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card dashboard-card bg-success text-white">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h5 class="card-title">My Articles</h5>
                    <p class="card-text fs-3 fw-bold"><?php echo $stats['my_articles']; ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="col-lg-<?php echo $userType === 'Super_User' ? '3' : '4'; ?> col-md-6 mb-4">
        <div class="card dashboard-card bg-warning text-dark">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h5 class="card-title">Total Articles</h5>
                <p class="card-text fs-3 fw-bold"><?php echo $stats['total_articles']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-bolt me-2"></i>
            Quick Actions
        </h4>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h5 class="card-title">Update Profile</h5>
                <p class="card-text">Update your personal information and change password.</p>
                <a href="profile.php" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Update Profile
                </a>
            </div>
        </div>
    </div>
    
    <?php if ($userType === 'Super_User'): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">Add, edit, delete and manage all system users.</p>
                    <a href="manage_users.php" class="btn btn-success">
                        <i class="fas fa-users-cog me-1"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
    <?php elseif ($userType === 'Administrator'): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h5 class="card-title">Manage Authors</h5>
                    <p class="card-text">Add, edit, delete and manage author accounts.</p>
                    <a href="manage_authors.php" class="btn btn-info">
                        <i class="fas fa-user-edit me-1"></i>Manage Authors
                    </a>
                </div>
            </div>
        </div>
    <?php elseif ($userType === 'Author'): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="card-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h5 class="card-title">Manage Articles</h5>
                    <p class="card-text">Create, edit and manage your articles.</p>
                    <a href="manage_articles.php" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Manage Articles
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h5 class="card-title">View Articles</h5>
                <p class="card-text">Browse and read the latest published articles.</p>
                <a href="view_articles.php" class="btn btn-info">
                    <i class="fas fa-eye me-1"></i>View Articles
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card dashboard-card h-100">
            <div class="card-body">
                <div class="card-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <h5 class="card-title">Logout</h5>
                <p class="card-text">Safely sign out of the system.</p>
                <a href="logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Latest Articles -->
<?php if (!empty($stats['latest_articles'])): ?>
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">
            <i class="fas fa-newspaper me-2"></i>
            Latest Articles
        </h4>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <?php foreach ($stats['latest_articles'] as $article): ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title article-title">
                                        <?php echo htmlspecialchars($article['article_title']); ?>
                                    </h6>
                                    <p class="card-text article-excerpt">
                                        <?php echo truncateText(strip_tags($article['article_full_text']), 100); ?>
                                    </p>
                                    <small class="text-muted article-meta">
                                        By <?php echo htmlspecialchars($article['author_name']); ?> • 
                                        <?php echo formatDate($article['article_created_date']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-3">
                    <a href="view_articles.php" class="btn btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>View All Articles
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
