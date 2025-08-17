<?php
/**
 * Manage Users Page - Super User Only
 * 
 * Allows Super Users to manage all other users
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/User.php';

// Require Super User access
SessionManager::requireUserType('Super_User');

$pageTitle = 'Manage Users';
$showNavigation = true;

$user = new User();
$currentUserId = SessionManager::getCurrentUserId();

$error = '';
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // Create new user
        $fullName = sanitizeInput($_POST['full_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phoneNumber = sanitizeInput($_POST['phone_number'] ?? '');
        $userName = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $userType = $_POST['user_type'] ?? '';
        $address = sanitizeInput($_POST['address'] ?? '');
        
        // Validation
        $errors = [];
        
        if (empty($fullName)) $errors[] = 'Full name is required.';
        if (empty($email) || !isValidEmail($email)) $errors[] = 'Valid email is required.';
        if (empty($userName) || !isValidUsername($userName)) $errors[] = 'Valid username is required.';
        if (empty($password) || !isValidPassword($password)) $errors[] = 'Valid password is required.';
        if (!in_array($userType, ['Administrator', 'Author'])) $errors[] = 'Valid user type is required.';
        
        if ($user->usernameExists($userName)) $errors[] = 'Username already exists.';
        if ($user->emailExists($email)) $errors[] = 'Email already exists.';
        
        if (empty($errors)) {
            $newUser = new User();
            $newUser->setFullName($fullName);
            $newUser->setEmail($email);
            $newUser->setPhoneNumber($phoneNumber);
            $newUser->setUserName($userName);
            $newUser->setPassword($password);
            $newUser->setUserType($userType);
            $newUser->setAddress($address);
            
            if ($newUser->create()) {
                $success = 'User created successfully!';
                logActivity("New user created: $userName ($userType)");
            } else {
                $error = 'Failed to create user.';
            }
        } else {
            $error = implode('<br>', $errors);
        }
    } elseif ($action === 'delete') {
        $userId = intval($_POST['user_id'] ?? 0);
        if ($userId && $userId !== $currentUserId) {
            $userToDelete = $user->getById($userId);
            if ($userToDelete && $user->delete($userId)) {
                $success = 'User deleted successfully!';
                logActivity("User deleted: " . $userToDelete['User_Name']);
            } else {
                $error = 'Failed to delete user.';
            }
        } else {
            $error = 'Cannot delete your own account.';
        }
    }
}

// Get all users except Super_User
$administrators = $user->getAllByType('Administrator');
$authors = $user->getAllByType('Author');

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-users-cog me-2"></i>
            Manage Users
        </h1>
        
        <?php echo generateBreadcrumb([
            ['text' => 'Dashboard', 'url' => 'dashboard.php'],
            ['text' => 'Manage Users', 'url' => '#']
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

<!-- Add New User Button -->
<div class="row mb-4">
    <div class="col-12">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fas fa-plus me-1"></i>Add New User
        </button>
    </div>
</div>

<!-- Administrators -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-tie me-2"></i>
                    Administrators (<?php echo count($administrators); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($administrators)): ?>
                    <p class="text-muted mb-0">No administrators found.</p>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($administrators as $admin): ?>
                                    <tr class="searchable-row">
                                        <td><?php echo htmlspecialchars($admin['Full_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['User_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['phone_Number'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($admin['AccessTime']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editUser(<?php echo $admin['userId']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="deleteUser(<?php echo $admin['userId']; ?>, '<?php echo htmlspecialchars($admin['Full_Name']); ?>')">
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

<!-- Authors -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>
                    Authors (<?php echo count($authors); ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($authors)): ?>
                    <p class="text-muted mb-0">No authors found.</p>
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
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($authors as $author): ?>
                                    <tr class="searchable-row">
                                        <td><?php echo htmlspecialchars($author['Full_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($author['User_Name']); ?></td>
                                        <td><?php echo htmlspecialchars($author['email']); ?></td>
                                        <td><?php echo htmlspecialchars($author['phone_Number'] ?? 'N/A'); ?></td>
                                        <td><?php echo formatDate($author['AccessTime']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="editUser(<?php echo $author['userId']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" onclick="deleteUser(<?php echo $author['userId']; ?>, '<?php echo htmlspecialchars($author['Full_Name']); ?>')">
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

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Add New User
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
                            <label for="user_type" class="form-label">User Type *</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="">Select User Type</option>
                                <option value="Administrator">Administrator</option>
                                <option value="Author">Author</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number">
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
                        <i class="fas fa-plus me-1"></i>Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Form (Hidden) -->
<form id="deleteUserForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="deleteUserId">
</form>

<script>
function editUser(userId) {
    // Redirect to edit page (we'll create this later)
    window.location.href = 'edit_user.php?id=' + userId;
}

function deleteUser(userId, userName) {
    if (confirm('Are you sure you want to delete user "' + userName + '"? This action cannot be undone.')) {
        document.getElementById('deleteUserId').value = userId;
        document.getElementById('deleteUserForm').submit();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
