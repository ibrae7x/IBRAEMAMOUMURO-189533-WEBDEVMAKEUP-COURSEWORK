<?php
/**
 * Manage Authors Page - Administrator Only
 * 
 * Allows Administrators to manage author accounts
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/User.php';

// Require Administrator access
SessionManager::requireUserType('Administrator');

$pageTitle = 'Manage Authors';
$showNavigation = true;

$user = new User();
$currentUserId = SessionManager::getCurrentUserId();

$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // Create new author
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phoneNumber = sanitizeInput($_POST['phone_number'] ?? '');
        $userName = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $address = sanitizeInput($_POST['address'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($fullName)) $errors[] = 'Full name is required.';
        if (empty($email) || !isValidEmail($email)) $errors[] = 'Valid email is required.';
        if (empty($userName) || !isValidUsername($userName)) $errors[] = 'Valid username is required.';
        if (empty($password) || !isValidPassword($password)) $errors[] = 'Valid password is required.';
        
        if ($user->usernameExists($userName)) $errors[] = 'Username already exists.';
        if ($user->emailExists($email)) $errors[] = 'Email already exists.';
        
        if (empty($errors)) {
            $newUser = new User();
            $newUser->setFullName($fullName);
            $newUser->setEmail($email);
            $newUser->setPhoneNumber($phoneNumber);
            $newUser->setUserName($userName);
            $newUser->setPassword($password);
            $newUser->setUserType('Author');
            $newUser->setAddress($address);
            
            if ($newUser->create()) {
                $success = 'Author created successfully!';
                logActivity("New author created: $userName");
            } else {
                $error = 'Failed to create author.';
            }
        } else {
            $error = implode('<br>', $errors);
        }
    } elseif ($action === 'delete') {
        $userId = intval($_POST['user_id'] ?? 0);
        if ($userId && $userId !== $currentUserId) {
            $userToDelete = $user->getById($userId);
            if ($userToDelete && $userToDelete['UserType'] === 'Author' && $user->delete($userId)) {
                $success = 'Author deleted successfully!';
                logActivity("Author deleted: " . $userToDelete['User_Name']);
            } else {
                $error = 'Failed to delete author.';
            }
        } else {
            $error = 'Cannot delete your own account.';
        }
    }
}

// Get all authors
$authors = $user->getAllByType('Author');

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-user-edit me-2"></i>
            Manage Authors
        </h1>
        
        <?php echo generateBreadcrumb([
            ['text' => 'Dashboard', 'url' => 'dashboard.php'],
            ['text' => 'Manage Authors', 'url' => '#']
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

<!-- Add New Author Button -->
<div class="row mb-4">
    <div class="col-12">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
            <i class="fas fa-plus me-1"></i>Add New Author
        </button>
    </div>
</div>

<!-- Authors List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Authors (<?php echo count($authors); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($authors)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-edit fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Authors Found</h5>
                        <p class="text-muted mb-3">There are no author accounts in the system yet. Click the button above to add the first author.</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                            <i class="fas fa-plus me-1"></i>Add First Author
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Last Access</th>
                                    <th>Articles</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Get article count for each author
                                require_once '../models/Article.php';
                                $article = new Article();
                                
                                foreach ($authors as $author): 
                                    $authorArticles = $article->getByAuthor($author['userId']);
                                    $publishedCount = array_filter($authorArticles, function($a) { return $a['article_display'] === 'yes'; });
                                ?>
                                    <tr class="searchable-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($author['profile_Image']): ?>
                                                    <img 
                                                        src="../uploads/profile_images/<?php echo htmlspecialchars($author['profile_Image']); ?>" 
                                                        alt="Profile" 
                                                        class="profile-image-small me-2"
                                                    >
                                                <?php else: ?>
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($author['Full_Name']); ?></strong>
                                                    <?php if ($author['Address']): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($author['Address']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($author['User_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($author['email']); ?></td>
                                        <td><?php echo htmlspecialchars($author['phone_Number'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($author['AccessTime']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo count($authorArticles); ?> Total</span>
                                            <span class="badge bg-success"><?php echo count($publishedCount); ?> Published</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editAuthor(<?php echo $author['userId']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info" onclick="viewAuthorArticles(<?php echo $author['userId']; ?>)">
                                                <i class="fas fa-newspaper"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteAuthor(<?php echo $author['userId']; ?>, '<?php echo htmlspecialchars($author['Full_Name']); ?>')">
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

<!-- Add Author Modal -->
<div class="modal fade" id="addAuthorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New Author
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number">
                        </div>
                        <div class="col-md-6">
                            <div class="form-label">User Type</div>
                            <input type="text" class="form-control" value="Author" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Create Author
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Author Form (Hidden) -->
<form id="deleteAuthorForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="deleteAuthorId">
</form>

<script>
function editAuthor(userId) {
    // Redirect to edit page (we'll create this later)
    window.location.href = 'edit_user.php?id=' + userId;
}

function viewAuthorArticles(userId) {
    // Redirect to view author's articles
    window.location.href = 'author_articles.php?author_id=' + userId;
}

function deleteAuthor(userId, authorName) {
    if (confirm('Are you sure you want to delete author "' + authorName + '"? This will also delete all their articles. This action cannot be undone.')) {
        document.getElementById('deleteAuthorId').value = userId;
        document.getElementById('deleteAuthorForm').submit();
    }
}

// Initialize search functionality
$(document).ready(function() {
    // Add search input if not exists
    if ($('#search-input').length === 0) {
        $('.card-header h5').after(
            '<div class="mt-2">' +
            '<input type="text" class="form-control" id="search-input" placeholder="Search authors..." data-target=".searchable-row">' +
            '</div>'
        );
    }
});
</script>

<?php include '../includes/footer.php'; ?>
