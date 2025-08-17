<?php
/**
 * Profile Management Page
 * 
 * Allows users to update their profile information
 */

require_once '../config/constants.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';
require_once '../models/User.php';

// Require login
SessionManager::requireLogin();

$pageTitle = 'Update Profile';
$showNavigation = true;

$userId = SessionManager::getCurrentUserId();
$currentUserType = SessionManager::getCurrentUserType();

$user = new User();
$userData = $user->getById($userId);

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phoneNumber = sanitizeInput($_POST['phone_number'] ?? '');
    $address = sanitizeInput($_POST['address'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Valid email is required.';
    }
    
    if (!empty($phoneNumber) && !isValidPhone($phoneNumber)) {
        $errors[] = 'Valid phone number is required.';
    }
    
    // Check if email exists for other users
    if ($user->emailExists($email, $userId)) {
        $errors[] = 'Email address is already in use by another user.';
    }
    
    // Password validation
    if (!empty($newPassword)) {
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required to change password.';
        } elseif (!password_verify($currentPassword, $userData['Password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (!isValidPassword($newPassword)) {
            $errors[] = 'New password must be at least 6 characters and contain letters and numbers.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirmation do not match.';
        }
    }
    
    if (empty($errors)) {
        // Update user
        $userObj = new User();
        $userObj->setUserId($userId);
        $userObj->setFullName($fullName);
        $userObj->setEmail($email);
        $userObj->setPhoneNumber($phoneNumber);
        $userObj->setAddress($address);
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['profile_image'], '../uploads/profile_images/');
            if ($uploadResult['success']) {
                // Delete old image if exists
                if ($userData['profile_Image'] && file_exists('../uploads/profile_images/' . $userData['profile_Image'])) {
                    unlink('../uploads/profile_images/' . $userData['profile_Image']);
                }
                $userObj->setProfileImage($uploadResult['filename']);
            } else {
                $errors[] = $uploadResult['message'];
            }
        } else {
            $userObj->setProfileImage($userData['profile_Image']);
        }
        
        // Set password if changed
        if (!empty($newPassword)) {
            $userObj->setPassword($newPassword);
        }
        
        if (empty($errors) && $userObj->update()) {
            $success = 'Profile updated successfully!';
            logActivity("Profile updated for user: " . $userData['User_Name']);
            
            // Update session data
            SessionManager::logout();
            if ($userObj->authenticate($userData['User_Name'], !empty($newPassword) ? $newPassword : $currentPassword)) {
                SessionManager::login($userObj);
            }
            
            // Refresh user data
            $userData = $user->getById($userId);
        } else {
            $error = 'Failed to update profile. Please try again.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

include '../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="h3 mb-4">
            <i class="fas fa-user-edit me-2"></i>
            Update My Profile
        </h1>
        
        <?php echo generateBreadcrumb([
            ['text' => 'Dashboard', 'url' => 'dashboard.php'],
            ['text' => 'Update Profile', 'url' => '#']
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

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Profile Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name *</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="full_name" 
                                name="full_name" 
                                value="<?php echo htmlspecialchars($userData['Full_Name']); ?>"
                                required
                            >
                            <div class="invalid-feedback">
                                Please provide a valid full name.
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                name="email" 
                                value="<?php echo htmlspecialchars($userData['email']); ?>"
                                required
                            >
                            <div class="invalid-feedback">
                                Please provide a valid email address.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input 
                                type="tel" 
                                class="form-control" 
                                id="phone_number" 
                                name="phone_number" 
                                value="<?php echo htmlspecialchars($userData['phone_Number'] ?? ''); ?>"
                            >
                        </div>
                        
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username (Read Only)</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="username" 
                                value="<?php echo htmlspecialchars($userData['User_Name']); ?>"
                                readonly
                                style="background-color: #f8f9fa;"
                            >
                            <small class="text-muted">Username cannot be changed</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea 
                            class="form-control" 
                            id="address" 
                            name="address" 
                            rows="3"
                        ><?php echo htmlspecialchars($userData['Address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input 
                            type="file" 
                            class="form-control" 
                            id="profile_image" 
                            name="profile_image" 
                            accept="image/*"
                        >
                        <small class="text-muted">Maximum file size: 2MB. Allowed formats: JPG, JPEG, PNG, GIF</small>
                        
                        <?php if ($userData['profile_Image']): ?>
                            <div class="mt-2">
                                <img 
                                    src="../uploads/profile_images/<?php echo htmlspecialchars($userData['profile_Image']); ?>" 
                                    alt="Current Profile Image" 
                                    class="profile-image-small"
                                >
                                <small class="text-muted ms-2">Current profile image</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">
                        <i class="fas fa-lock me-2"></i>
                        Change Password (Optional)
                    </h6>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="current_password" 
                                name="current_password"
                                autocomplete="current-password"
                            >
                        </div>
                        
                        <div class="col-md-4">
                            <label for="new_password" class="form-label">New Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="new_password" 
                                name="new_password"
                                autocomplete="new-password"
                            >
                        </div>
                        
                        <div class="col-md-4">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                id="confirm_password" 
                                name="confirm_password"
                                autocomplete="new-password"
                            >
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Account Information
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>User Type:</strong></td>
                        <td>
                            <span class="badge bg-primary">
                                <?php echo getUserTypeDisplayName($userData['UserType']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Member Since:</strong></td>
                        <td><?php echo formatDate($userData['created_at'], 'M d, Y'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Last Access:</strong></td>
                        <td><?php echo formatDate($userData['AccessTime'], 'M d, Y H:i'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td><?php echo formatDate($userData['updated_at'], 'M d, Y H:i'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Security Tips
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Use a strong password with letters, numbers, and symbols
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Never share your login credentials
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        Always log out when finished
                    </li>
                    <li>
                        <i class="fas fa-check text-success me-2"></i>
                        Keep your contact information updated
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
