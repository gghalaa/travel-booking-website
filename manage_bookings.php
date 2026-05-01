<?php
session_start();

// Initialize DB connection
include 'db_config.php';

// Handle form actions (confirm or cancel booking)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'];

    // Confirm booking
    if ($action === 'confirm') {
        $stmt = $conn->prepare("UPDATE bookings SET status='confirmed' WHERE booking_id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Booking #$booking_id successfully confirmed.";
    } 
    // Cancel booking
    elseif ($action === 'cancel') {
        $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE booking_id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Booking #$booking_id successfully cancelled.";
    }
    header("Location: manage_bookings.php"); // Redirect to manage bookings page after action
    exit();
}

// Fetch bookings with associated trip and user info
$sql = "SELECT b.*, t.title AS trip_title, u.email AS user_email 
        FROM bookings b 
        JOIN trips t ON b.trip_id = t.trip_id 
        JOIN users u ON b.user_id = u.user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Bookings - Dreamscape Destinations</title>
  <link rel="stylesheet" href="assets/css/styles.css"> <!-- External stylesheet for common styles -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    .section-title {
      margin-bottom: 50px; /* Margin below the section title */
    }
    .custom-table th {
      background-color: #4a4e69; /* Table header background color */
      color: white; /* Table header text color */
      text-align: center; /* Center text in the header cells */
      padding: 12px; /* Padding for header cells */
    }
    .btn-action {
      display: block; /* Stack buttons vertically */
      width: 100%; /* Make buttons span full width */
      margin-bottom: 10px; /* Space between buttons */
      text-align: center;
    }
    .btn-second {
      background-color: #6c757d; /* Button background color */
      color: #f8f9fa; /* Button text color */
      border: none; /* Remove border */
      border-radius: 5px; /* Rounded corners */
      text-decoration: none;
      text-align: center;
    }
    .btn-second:hover {
      background-color: #9a8c98; /* Change color on hover */
      color: black; /* Change text color on hover */
    }
    .btn-second:active {
      background-color: transparent; /* Remove background on active */
      color: inherit; /* Keep original text color */
    }
    .cancelled-row {
      background-color: #e9ecef; /* Background color for cancelled bookings */
      color: #6c757d; /* Text color for cancelled bookings */
    }
    .center-buttons {
      text-align: center; /* Center the action buttons */
    }
    input[readonly], input[disabled] {
      background-color: #f1f1f1 !important; /* Change background for readonly or disabled inputs */
    }
    td input[name="user_email"],
    td input[name="trip_title"] {
      max-width: 180px; /* Max width for email and trip title inputs */
      padding: 4px 6px; /* Padding for input fields */
      font-size: 0.9rem; /* Font size for input fields */
      text-align: center; /* Center text inside the input */
      display: block;
      margin: 0 auto; /* Center the input horizontally */
    }
    td input[name="booking_date"] {
      max-width: 120px; /* Max width for booking date */
      padding: 4px 6px;
      font-size: 0.9rem;
      text-align: center;
      display: block;
      margin: 0 auto;
    }
    td input[name="start_date"],
    td input[name="end_date"],
    td input[name="passengers"],
    td input[name="class"],
    td input[name="status"] {
      max-width: 110px; /* Max width for other fields */
      padding: 4px 6px;
      font-size: 0.9rem;
      text-align: center;
      display: block;
      margin: 0 auto;
    }
    td:nth-child(3) {
      min-width: 140px; /* Set minimum width for booking date column */
      white-space: nowrap; /* Prevent text from wrapping */
      font-size: 0.95rem;
    }
    td.center-buttons {
      width: 150px; /* Fix width for action buttons column */
    }
    td input[name="class"],
    td input[name="status"] {
      max-width: 100px; /* Max width for class and status fields */
    }
    input.status-pending {
      background-color: #fff3cd !important; /* Pending status background color */
      color: #856404 !important; /* Text color for pending status */
      border: 1px solid #ffeeba !important; /* Border color for pending status */
      font-weight: bold;
    }
    input.status-confirmed {
      background-color: #d4edda !important; /* Confirmed status background color */
      color: #155724 !important; /* Text color for confirmed status */
      border: 1px solid #c3e6cb !important; /* Border color for confirmed status */
      font-weight: bold;
    }
    input.status-cancelled {
      background-color: #f8d7da !important; /* Cancelled status background color */
      color: #721c24 !important; /* Text color for cancelled status */
      border: 1px solid #f5c6cb !important; /* Border color for cancelled status */
      font-weight: bold;
    }
    td {
      text-align: center; /* Center content inside table cells */
      vertical-align: middle; /* Vertically center content */
    }
    input.form-control {
      display: block;
      margin: 0 auto;
      text-align: center;
    }
    td input[name="user_email"],
    td input[name="trip_title"],
    td input[name="booking_date"],
    td input[name="start_date"],
    td input[name="end_date"],
    td input[name="passengers"],
    td input[name="class"],
    td input[name="status"] {
      max-width: 180px; /* Adjust max width for better layout */
      padding: 4px 6px;
      font-size: 0.9rem;
      margin: 0 auto;
      display: block;
      text-align: center;
    }
    @media (max-width: 768px) {
      .navbar-brand {
        display: block;
        text-align: center;
      }
      .navbar-brand span {
        display: block; /* Stack the navbar brand text */
      }
    }
    .custom-table th,
    .custom-table td input {
      min-width: 160px; /* Set minimum width for table columns */
      font-size: 0.95rem;
    }
    td input[name="user_email"],
    td input[name="trip_title"] {
      min-width: 200px; /* Set min width for user email and trip title */
    }
    td input[name="booking_date"],
    td input[name="start_date"],
    td input[name="end_date"],
    td input[name="passengers"],
    td input[name="class"],
    td input[name="status"] {
      min-width: 140px; /* Set min width for other columns */
    }
  </style>
</head>

<body>
  <!-- Admin Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <!-- Brand and link to admin dashboard -->
      <a class="qwigley-regular navbar-brand" href="admin.php">
        Dreamscape Destinations <span>- Admin</span>
      </a>
      <!-- Navbar toggler for mobile view -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <!-- Dashboard link -->
          <li class="nav-item">
            <a class="nav-link" href="admin.php">
              <span class="material-symbols-outlined">home</span> Dashboard
            </a>
          </li>
          <!-- Manage Trips link -->
          <li class="nav-item">
            <a class="nav-link" href="manage_trips.php">
              <span class="material-symbols-outlined">map</span> Manage Trips
            </a>
          </li>
          <!-- Manage Bookings link (active) -->
          <li class="nav-item">
            <a class="nav-link active" href="manage_bookings.php">
              <span class="material-symbols-outlined">book</span> Manage Bookings
            </a>
          </li>
          <!-- Manage Users link -->
          <li class="nav-item">
            <a class="nav-link" href="manage_users.php">
              <span class="material-symbols-outlined">person</span> Manage Users
            </a>
          </li>
          <!-- Logout link if admin is logged in -->
          <?php if (isset($_SESSION['admin'])): ?>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">
                <span class="material-symbols-outlined">logout</span> Logout
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Success message display if set -->
  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success" role="alert">
      <?= $_SESSION['success_message']; ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <!-- Manage Bookings Section -->
  <div class="container my-5">
    <h2 class="section-title">Manage Bookings</h2>
    <!-- Table to display bookings -->
    <div style="overflow-x:auto;">
      <table class="table table-bordered custom-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Trip</th>
            <th>Booking Date</th>
            <th>Start</th>
            <th>End</th>
            <th>Passengers</th>
            <th>Class</th>
            <th>Status</th>
            <th class="center-buttons">Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through each booking and display in table -->
          <?php while ($booking = $result->fetch_assoc()): ?>
            <form method="post" id="bookingForm<?= $booking['booking_id'] ?>">
              <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
              <tr class="<?= $booking['status'] === 'cancelled' ? 'cancelled-row' : '' ?>">
                <td><input type="text" name="user_email" class="form-control" value="<?= htmlspecialchars($booking['user_email']) ?>" readonly></td>
                <td><input type="text" name="trip_title" class="form-control" value="<?= htmlspecialchars($booking['trip_title']) ?>" readonly></td>
                <td><input type="text" name="booking_date" class="form-control" value="<?= date("M d, Y", strtotime($booking['booking_date'])) ?>" readonly></td>
                <td><input type="text" name="start_date" class="form-control" value="<?= date("M d, Y", strtotime($booking['start_date'])) ?>" readonly></td>
                <td><input type="text" name="end_date" class="form-control" value="<?= date("M d, Y", strtotime($booking['end_date'])) ?>" readonly></td>
                <td><input type="number" name="passengers" class="form-control" value="<?= $booking['passengers'] ?>" readonly></td>
                <td><input type="text" name="class" class="form-control" value="<?= $booking['class'] ?>" readonly></td>
                <td>
                  <input type="text" name="status" class="form-control <?= 
                    $booking['status'] === 'pending' ? 'status-pending' : 
                    ($booking['status'] === 'confirmed' ? 'status-confirmed' : 'status-cancelled') ?>" 
                    value="<?= ucfirst($booking['status']) ?>" readonly>
                </td>
                <td class="center-buttons">
                  <!-- Action buttons for pending bookings -->
                  <?php if ($booking['status'] === 'pending'): ?>
                    <div class="button-group">
                      <button type="button" class="btn btn-theme btn-action" data-bs-toggle="modal" data-bs-target="#confirmModal<?= $booking['booking_id'] ?>">Confirm</button>
                      <button type="button" class="btn btn-second btn-action" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $booking['booking_id'] ?>">Cancel</button>
                    </div>
                  <?php elseif ($booking['status'] === 'confirmed'): ?>
                    <!-- Action button for confirmed bookings -->
                    <div class="button-group">
                      <button type="button" class="btn btn-second btn-action" data-bs-toggle="modal" data-bs-target="#cancelModal<?= $booking['booking_id'] ?>">Cancel</button>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            </form>

            <!-- Confirm Modal -->
            <div class="modal fade" id="confirmModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="card-title modal-title">Confirm Booking</h5>
                  </div>
                  <div class="modal-body">Are you sure you want to confirm this booking?</div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-theme" onclick="submitForm(<?php echo $booking['booking_id']; ?>, 'confirm')">Yes, Confirm</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Cancel Modal -->
            <div class="modal fade" id="cancelModal<?php echo $booking['booking_id']; ?>" tabindex="-1">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="card-title modal-title">Cancel Booking</h5>
                  </div>
                  <div class="modal-body">Are you sure you want to cancel this booking?</div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-theme" onclick="submitForm(<?php echo $booking['booking_id']; ?>, 'cancel')">Yes</button>
                  </div>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Script for form submission -->
  <script>
    function submitForm(bookingId, action) {
      const form = document.getElementById('bookingForm' + bookingId);
      const hiddenAction = document.createElement('input');
      hiddenAction.type = 'hidden';
      hiddenAction.name = 'action';
      hiddenAction.value = action;
      let existing = form.querySelector('input[name="action"]');
      if (existing) existing.remove();
      form.appendChild(hiddenAction);
      form.submit();
    }
  </script>

  <!-- Footer section -->
  <footer class="text-center py-3">
    <p class="mb-0">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
