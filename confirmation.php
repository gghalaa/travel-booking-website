<?php
session_start();

// Check if booking was successful, if not redirect
if (!isset($_SESSION['booking_success']) || !$_SESSION['booking_success']) {
    header("Location: home.php"); 
    exit();
}

// Retrieve session variables related to the booking
$trip_id = $_SESSION['trip_id'];
$trip_title = $_SESSION['trip_title'];
$trip_location = $_SESSION['trip_location'];
$checkin = $_SESSION['checkin'];
$checkout = $_SESSION['checkout'];
$passengers = $_SESSION['passengers'];
$class = $_SESSION['class'];
$booking_id = $_SESSION['booking_id'];  

// Include database connection file
include 'db_config.php';

// Fetch the price for the selected trip from the database
$price_stmt = $conn->prepare("SELECT price FROM trips WHERE trip_id = ?");
$price_stmt->bind_param("i", $trip_id);
$price_stmt->execute();
$price_stmt->bind_result($trip_price);

// Check if trip price is found
if (!$price_stmt->fetch()) {
    die("Trip not found or price unavailable.");
}
$price_stmt->close();

// Calculate additional charges based on the selected class
$class_extra = 0;
if ($class === 'business') {
    $class_extra = 100; 
} elseif ($class === 'first-class') {
    $class_extra = 200; 
}

// Calculate the total amount for the booking based on price, class, and number of passengers
$total_amount = ($trip_price + $class_extra) * $passengers;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dreamscape Destinations - Booking Confirmation</title>
  
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    .subtitle-value {
      color: inherit !important;
      text-decoration: none !important;
      pointer-events: none;
      cursor: default;
    }
  </style>
</head>

<body>
  <div class="d-flex flex-column min-vh-100">

    <!-- Navbar Section -->
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="container">
        <a class="qwigley-regular navbar-brand" href="home.php">Dreamscape Destinations</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav">
            <!-- Navigation Links -->
            <li class="nav-item">
              <a class="nav-link" href="home.php">
                <span class="material-symbols-outlined">home</span> Home
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="trips.php">
                <span class="material-symbols-outlined">map</span> Trips
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="aboutus.php">
                <span class="material-symbols-outlined">info</span> About Us
              </a>
            </li>
            <!-- Conditional Login / Logout Links -->
            <?php if (isset($_SESSION['user_id'])): ?>
              <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                  <span class="material-symbols-outlined">account_circle</span> Dashboard
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">
                  <span class="material-symbols-outlined">logout</span> Logout
                </a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="account.php">
                  <span class="material-symbols-outlined">login</span> Login / Sign Up
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-5 flex-grow-1">
      <h2 class="section-title">Thank You For Your Booking!</h2>
      <p>Your booking is currently marked as <strong>pending</strong> and will remain unconfirmed until full payment of the amount below has been received.</p>

      <!-- Booking Summary -->
      <h4 class="mt-4 mb-3 card-title">Booking Summary:</h4>
      <p class="subtitle">
        <span class="subtitle-label"><strong>Booking ID:</strong></span>
        <span class="subtitle-value">
          <?php echo isset($_SESSION['booking_id']) ? htmlspecialchars($_SESSION['booking_id']) : 'Not available'; ?>
        </span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Destination:</strong></span>
        <span class="subtitle-value"><?php echo htmlspecialchars($trip_title) . " (" . htmlspecialchars($trip_location) . ")"; ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Check-in Date:</strong></span>
        <span class="subtitle-value"><?php echo htmlspecialchars($checkin); ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Check-out Date:</strong></span>
        <span class="subtitle-value"><?php echo htmlspecialchars($checkout); ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Number of Passengers:</strong></span>
        <span class="subtitle-value"><?php echo htmlspecialchars($passengers); ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Class:</strong></span>
        <span class="subtitle-value"><?php echo ucfirst(htmlspecialchars($class)); ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Total Amount:</strong></span>
        <span class="subtitle-value">$<?php echo number_format($total_amount, 2); ?></span>
      </p>

      <!-- Payment Instructions -->
      <h4 class="mt-5 mb-3 card-title">How to Confirm Your Booking:</h4>
      <p>To complete your reservation, please visit one of our authorized branches or transfer the total amount to our bank account in the next 48 hours using the details below:</p>

      <!-- Bank Transfer Details -->
      <p class="subtitle">
        <span class="subtitle-label"><strong>Total Amount:</strong></span>
        <span class="subtitle-value">$<?= number_format($total_amount, 2) ?></span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Bank Name:</strong></span>
        <span class="subtitle-value">DreamBank International</span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Account Name:</strong></span>
        <span class="subtitle-value">Dreamscape Destinations Ltd.</span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Account Number:</strong></span>
        <span class="subtitle-value" style="color: inherit; text-decoration: none; pointer-events: none; cursor: default; user-select: text;">
          <span style="white-space: nowrap;">123<span style="display:none"> </span>-456<span style="display:none"> </span>-789</span>
        </span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>IBAN:</strong></span>
        <span class="subtitle-value">DBIN0000123456789</span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>SWIFT Code:</strong></span>
        <span class="subtitle-value">DRMBUS33</span>
      </p>

      <p class="subtitle">
        <span class="subtitle-label"><strong>Branch Address:</strong></span>
        <span class="subtitle-value">123 Paradise Blvd, Travel City, TX 54321, USA</span>
      </p>

      <p class="mt-3">Once the payment is made, kindly send the payment receipt or confirmation to <strong>payments@dreamscapedestinations.com</strong>. We appreciate your prompt attention and look forward to hosting your dream journey.</p>

      <!-- Action Buttons -->
      <div class="mt-4 mb-4 text-center">
        <a class="btn btn-theme btn-custom me-2" href="dashboard.php">View My Booking</a>
        <a class="btn btn-theme btn-custom" href="home.php">Back to Home</a>
      </div>
    </main>

    <!-- Footer Section -->
    <footer>
      <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
    </footer>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
