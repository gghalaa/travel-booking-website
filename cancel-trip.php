<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Initialize DB connection
include 'db_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit;
}

// Handle the POST request when the cancel button is clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'])) {
    // Get the booking ID and user ID
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];

    // Prepare SQL statement to update the booking status
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?");

    // Execute the prepared statement if it's valid
    if ($stmt) {
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Set success message and redirect to dashboard
        $_SESSION['success_message'] = "Trip cancelled successfully!";
        header("Location: dashboard.php");
        exit;
    } else {
        // Show error message if the SQL statement fails
        echo "Database error: " . $conn->error;
    }
} else {
    // Show an error message if the request is invalid
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>
