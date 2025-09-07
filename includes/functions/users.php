<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');


/**
 * Register a new user
 * 
 * @param array $userData User data (full_name, email, password, role_id)
 * @return array Result with status and message/user ID
 */
function registerUser($userData) {
    // Validate input
    if (empty($userData['full_name']) || empty($userData['email']) || empty($userData['password'])) {
        return ['status' => 'error', 'message' => 'All fields are required'];
    }
    
    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        return ['status' => 'error', 'message' => 'Invalid email format'];
    }
    
    // Check if email exists
    if (recordExists('users', 'email = ?', [$userData['email']])) {
        return ['status' => 'error', 'message' => 'Email already registered'];
    }
    
    // Hash password
    $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Prepare user data
    $data = [
        'full_name' => $userData['full_name'],
        'email' => $userData['email'],
        'password' => $hashedPassword,
        'role_id' => $userData['role_id'] ?? 2 // Default to regular user role
    ];
    
    // Insert user
    $userId = insertRecord('users', $data);
    
    if ($userId) {
        return ['status' => 'success', 'user_id' => $userId];
    } else {
        return ['status' => 'error', 'message' => 'Registration failed'];
    }
}

/**
 * Authenticate a user
 * 
 * @param string $email User email
 * @param string $password User password
 * @return array Result with status and message/user data
 */
function loginUser($email, $password) {
    // Get user by email
    $user = fetchSingle("SELECT * FROM users WHERE email = ?", [$email]);
    
    if (!$user) {
        return ['status' => 'error', 'message' => 'Invalid email or password'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['status' => 'error', 'message' => 'Invalid email or password'];
    }
    
    // Check if user is active
    if (!$user['is_active']) {
        return ['status' => 'error', 'message' => 'Account is inactive'];
    }
    
    // Remove password before returning
    unset($user['password']);
    
    return ['status' => 'success', 'user' => $user];
}

/**
 * Get user by ID
 * 
 * @param int $userId User ID
 * @return array|false User data or false if not found
 */
function getUserById($userId) {
    $user = fetchSingle("SELECT id, full_name, email, role_id, is_active, created_at FROM users WHERE id = ?", [$userId]);
    
    if ($user) {
        $user['role_name'] = getUserRoleName($user['role_id']);
    }
    
    return $user;
}

/**
 * Get user role name
 * 
 * @param int $roleId Role ID
 * @return string Role name
 */
function getUserRoleName($roleId) {
    $role = fetchSingle("SELECT role_name FROM roles WHERE id = ?", [$roleId]);
    return $role ? $role['role_name'] : 'Unknown';
}

/**
 * Update user profile
 * 
 * @param int $userId User ID
 * @param array $data Updated user data
 * @return bool True on success, false on failure
 */
function updateUserProfile($userId, $data) {
    // Remove password if not changing it
    if (empty($data['password'])) {
        unset($data['password']);
    } else {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    
    $result = updateRecord('users', $data, 'id = ?', [$userId]);
    return $result !== false;
}

/**
 * Get all users (for admin)
 * 
 * @return array List of users
 */
function getAllUsers() {
    return fetchAll("
        SELECT u.id, u.full_name, u.email, r.role_name, u.is_active, u.created_at 
        FROM users u
        JOIN roles r ON u.role_id = r.id
        ORDER BY u.created_at DESC
    ");
}