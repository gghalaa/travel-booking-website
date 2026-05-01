<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required form data is provided
    if (!isset($_POST['trip'], $_POST['start_date'], $_POST['end_date'], $_POST['passengers'], $_POST['class'])) {
        header("Location: home.php");
        exit();
    }

    // Get session and form data
    $user_id = $_SESSION['user_id'];
    $trip_id = intval($_POST['trip']);
    $booking_date = date('Y-m-d');
    $status = 'pending';
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $passengers = intval($_POST['passengers']);
    $class = trim($_POST['class']);
    
    // Validate class
    $valid_classes = ['economy', 'business', 'first-class'];
    if (!in_array($class, $valid_classes)) {
        die("Invalid class selected.");
    }

    // Validate passengers
    if ($passengers <= 0) {
        header("Location: home.php");
        exit();
    }

    // Connect to database and fetch trip details
    include 'db_config.php';
    $stmt = $conn->prepare("SELECT price, title, location FROM trips WHERE trip_id = ?");
    if (!$stmt) {
        die("Prepare statement failed.");
    }

    $stmt->bind_param("i", $trip_id);
    $stmt->execute();
    $stmt->bind_result($base_price_per_passenger, $trip_title, $trip_location);
    if (!$stmt->fetch()) {
        die("Trip not found.");
    }
    $stmt->close();

    // Calculate the total amount
    $amount = $base_price_per_passenger * $passengers;
    if ($class == 'business') {
        $amount += 100 * $passengers; // Business class extra
    } elseif ($class == 'first-class') {
        $amount += 200 * $passengers; // First class extra
    }

    // Validate calculated amount
    if ($amount <= 0) {
        die("Invalid amount calculated.");
    }

    // Insert booking data into the database
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, trip_id, booking_date, status, start_date, end_date, passengers, class, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("Prepare statement failed.");
    }

    $stmt->bind_param("iisssssis", $user_id, $trip_id, $booking_date, $status, $start_date, $end_date, $passengers, $class, $amount);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;

        // Store booking details in session for confirmation page
        $_SESSION['booking_id'] = $booking_id;
        $_SESSION['booking_success'] = true;
        $_SESSION['trip_id'] = $trip_id;
        $_SESSION['trip_title'] = $trip_title;
        $_SESSION['trip_location'] = $trip_location;
        $_SESSION['checkin'] = $start_date;
        $_SESSION['checkout'] = $end_date;
        $_SESSION['passengers'] = $passengers;
        $_SESSION['class'] = $class;
        $_SESSION['amount'] = $amount;

        // Redirect to confirmation page
        header("Location: confirmation.php");
        exit();
    } else {
        die("Failed to insert booking.");
    }

    $stmt->close();
    $conn->close();
} else {
    // If not a POST request, redirect to home page
    header("Location: home.php");
    exit();
}
?>
