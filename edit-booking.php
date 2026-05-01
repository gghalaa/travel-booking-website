<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if booking_id is passed via POST
if (!isset($_POST['booking_id'])) {
    die("Booking ID is missing.");
}

$booking_id = $_POST['booking_id'];

// Initialize DB connection
include 'db_config.php';

// Fetch the current booking details to check if it's editable
$sql = "SELECT b.*, t.price FROM bookings b
        JOIN trips t ON b.trip_id = t.trip_id
        WHERE b.booking_id = ? AND b.user_id = ? AND b.status != 'cancelled'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

// If no booking found or the booking is not editable, stop execution
if (!$booking) {
    die("Booking not found or not editable.");
}

// Get the form data (new details for the booking)
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$passengers = (int) $_POST['passengers'];
$class = $_POST['class'];

// Get the base price for economy (one person)
$basePrice = $booking['price'];

// Define additional cost based on class selection
$additionalCost = 0;
if ($class == 'business') {
    $additionalCost = 100;
} elseif ($class == 'first-class') {
    $additionalCost = 200;
}

// Calculate the total price
$totalPrice = ($basePrice + $additionalCost) * $passengers;

// Update the booking details in the database
$update_sql = "UPDATE bookings SET start_date=?, end_date=?, passengers=?, class=?, amount=? WHERE booking_id=? AND user_id=?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ssisiii", $start_date, $end_date, $passengers, $class, $totalPrice, $booking_id, $user_id);
$update_stmt->execute();

// Check if the update was successful
if ($update_stmt->affected_rows > 0) {
    $_SESSION['success_message'] = "Booking updated successfully.";
    header("Location: dashboard.php");
    exit;
} else {
    echo "No changes were made or booking could not be updated.";
}

$update_stmt->close();
$conn->close();
?>
