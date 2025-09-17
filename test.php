<?php
session_start();

// Optional: Manually set values for testing
// $_SESSION['user_id'] = 123;
// $_SESSION['user_role'] = 'admin';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Session Variable Test</title>
</head>
<body>

<h2>Session Variable Test</h2>

<?php
// Test user_id
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>user_id:</strong> " . htmlspecialchars($_SESSION['user_id']) . "</p>";
} else {
    echo "<p style='color: red;'>❌ 'user_id' is NOT set in session.</p>";
}

// Test user_role
if (isset($_SESSION['user_role'])) {
    echo "<p><strong>user_role:</strong> " . htmlspecialchars($_SESSION['user_role']) . "</p>";

    // Additional check for admin role
    if ($_SESSION['user_role'] === 'admin') {
        echo "<p style='color: green;'>✅ user_role is 'admin'.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ user_role is set but NOT 'admin'.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ 'user_role' is NOT set in session.</p>";
}
?>

</body>
</html>
