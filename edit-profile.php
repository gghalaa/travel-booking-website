<?php
session_start();

// Initialize DB connection
include 'db_config.php';

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'You must be logged in to edit your profile.';
    header("Location: account.php");
    exit;
}

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, password_hash FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$has_error = false;

// Handling form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];
    $update_password = false;

    // Check if both new password and confirm password are provided
    if (!empty($_POST['new_password']) || !empty($_POST['confirm_password'])) {
        if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
            $_SESSION['error_message'] = 'Please fill in all password fields to change your password.';
            $_SESSION['show_modal'] = true;
            header("Location: dashboard.php");
            exit;
        }
    }

    // Check if new password and confirm password match
    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        $_SESSION['error_message'] = 'New password and confirm password must match.';
        $_SESSION['show_modal'] = true;
        header("Location: dashboard.php");
        exit;
    }

    // Check if current password and new password are provided and valid
    if (!empty($_POST['current_password']) && !empty($_POST['new_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Validate current password
        if (!password_verify($current_password, $user['password_hash'])) {
            $_SESSION['error_message'] = 'Current password is incorrect.';
            $has_error = true;
        } elseif (password_verify($new_password, $user['password_hash'])) {
            $_SESSION['error_message'] = 'New password cannot be the same as the current password.';
            $has_error = true;
        } elseif (strlen($new_password) < 6) {
            $_SESSION['error_message'] = 'New password must be at least 6 characters long.';
            $has_error = true;
        } else {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password = true;
        }
    }

    // Update user profile if no errors
    if (!$has_error) {
        if ($update_password) {
            // Update username and password
            $sql = "UPDATE users SET username = ?, password_hash = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $new_username, $new_password_hash, $user_id);
        } else {
            // Update only username
            $sql = "UPDATE users SET username = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_username, $user_id);
        }

        // Execute the update query
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error_message'] = 'Error updating profile. Please try again.';
            $_SESSION['show_modal'] = true;
        }

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // If there's an error, show the modal and redirect
        $_SESSION['show_modal'] = true;
        header("Location: dashboard.php");
        exit;
    }
}
?>