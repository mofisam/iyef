<?php
require_once '../config/db.php';
require_once '../includes/functions/users.php';

// Check admin access


$action = $_GET['action'] ?? 'list';
$userId = $_GET['id'] ?? 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $userData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'role_id' => (int)$_POST['role_id']
        ];

        // Only update password if provided
        if (!empty($_POST['password'])) {
            $userData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }

        if ($action === 'add') {
            if (empty($_POST['password'])) {
                $error = 'Password is required for new users';
            } else {
                $result = registerUser($userData);
                if ($result['status'] === 'success') {
                    $_SESSION['success_message'] = 'User added successfully!';
                    header('Location: users.php');
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
        } elseif ($action === 'edit') {
            if (updateUserProfile($userId, $userData)) {
                $_SESSION['success_message'] = 'User updated successfully!';
                header('Location: users.php');
                exit;
            } else {
                $error = 'Failed to update user';
            }
        }
    }
}

// Handle delete action
if ($action === 'delete') {
    // Prevent deleting own account
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'You cannot delete your own account!';
    } else {
        if (deleteRecord('users', 'id = ?', [$userId])) {
            $_SESSION['success_message'] = 'User deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete user';
        }
    }
    header('Location: users.php');
    exit;
}

// Handle toggle status action
if ($action === 'toggle-status') {
    // Prevent deactivating own account
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['error_message'] = 'You cannot deactivate your own account!';
    } else {
        $user = getUserById($userId);
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            updateRecord('users', ['is_active' => $newStatus], 'id = ?', [$userId]);
            $_SESSION['success_message'] = 'User status updated!';
        }
    }
    header('Location: users.php');
    exit;
}

// Set page title and breadcrumb
$page_title = "User Management";
$breadcrumb = [
    ['title' => 'Users', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if ($action === 'list'): ?>
        <!-- Users List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">All Users</h5>
                <a href="users.php?action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $users = getAllUsers();
                            foreach ($users as $user):
                                $createdAt = new DateTime($user['created_at']);
                            ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                                </td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['role_name'] ?></td>
                                <td>
                                    <?php if ($user['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $createdAt->format('M j, Y') ?></td>
                                <td class="table-actions">
                                    <a href="users.php?action=edit&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="profile.php?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Profile">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <a href="users.php?action=toggle-status&id=<?= $user['id'] ?>" 
                                       class="btn btn-sm <?= $user['is_active'] ? 'btn-outline-warning' : 'btn-outline-success' ?>" 
                                       title="<?= $user['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                        <i class="fas <?= $user['is_active'] ? 'fa-times' : 'fa-check' ?>"></i>
                                    </a>
                                    <a href="users.php?action=delete&id=<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No users found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- User Form -->
        <?php
        $user = [];
        $roles = fetchAll("SELECT * FROM roles ORDER BY role_name");
        
        if ($action === 'edit') {
            $user = getUserById($userId);
            if (!$user) {
                $_SESSION['error_message'] = 'User not found';
                header('Location: users.php');
                exit;
            }
        }
        
        if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> User</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <!-- Full Name -->
                        <div class="col-md-12">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Email -->
                        <div class="col-md-12">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Password -->
                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                <?= $action === 'add' ? 'Password' : 'New Password' ?> 
                                <?php if ($action === 'add'): ?><span class="text-danger">*</span><?php endif; ?>
                            </label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   <?= $action === 'add' ? 'required' : '' ?>>
                            <?php if ($action === 'edit'): ?>
                                <div class="form-text">Leave blank to keep current password</div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Role -->
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= isset($user['role_id']) && $user['role_id'] == $role['id'] ? 'selected' : '' ?>>
                                        <?= $role['role_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Add User' : 'Update User' ?></button>
                            <a href="users.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>