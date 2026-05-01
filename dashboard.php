<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to access session variables (like user info)
session_start();

// Initialize DB connection
include 'db_config.php';

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: account.php");
    exit;
}

// Get the user ID from the session to fetch personalized data
$user_id = $_SESSION['user_id'];

// Fetch user details (username and email) from the database
$sql = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch upcoming trips (excluding cancelled ones)
$sql = "SELECT t.title, t.location, b.start_date, b.end_date, b.booking_id, b.status, b.passengers, b.class, t.price
        FROM bookings b
        JOIN trips t ON b.trip_id = t.trip_id
        WHERE b.user_id = ? AND b.start_date > NOW() AND b.status != 'cancelled'
        ORDER BY b.start_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$upcomingTrips = $stmt->get_result();

// Fetch current trips (trips that are in progress, excluding cancelled ones)
$sql = "SELECT t.title, t.location, b.start_date, b.end_date, b.booking_id, b.status, b.passengers, b.class, t.price
        FROM bookings b
        JOIN trips t ON b.trip_id = t.trip_id
        WHERE b.user_id = ? AND b.start_date <= NOW() AND b.end_date >= NOW() AND b.status != 'cancelled'
        ORDER BY b.start_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$currentTrips = $stmt->get_result();

// Fetch past trips (trips that are completed, excluding cancelled ones)
$sql = "SELECT t.title, t.location, b.start_date, b.end_date, b.booking_id, b.status, b.passengers, b.class, t.price
        FROM bookings b
        JOIN trips t ON b.trip_id = t.trip_id
        WHERE b.user_id = ? AND b.end_date < NOW() AND b.status != 'cancelled'
        ORDER BY b.start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pastTrips = $stmt->get_result();

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags for character set and responsiveness -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Dashboard - Dreamscape Destinations</title>

  <!-- Link to external styles and fonts -->
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <style>
    /* Dashboard container styling */
    .dashboard-container {
      max-width: 1000px;
      margin: 60px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    /* Card title styling */
    .card-title {
      margin-bottom: 20px;
    }

    /* Form title styling */
    .form-title {
      text-align: center;
      margin-bottom: 25px;
      font-size: 1.8rem;
      font-weight: bold;
      color: #4a4e69;
      font-family: 'Trebuchet MS', sans-serif;
    }

    /* Form label styling */
    .form-label {
      font-family: 'Trebuchet MS', sans-serif;
      color: #4a4e69;
      font-weight: bold;
    }

    /* Dashboard heading styling */
    .dashboard-heading {
      font-size: 2rem;
      margin-bottom: 30px;
    }

    /* Styling for trip items */
    .trip-item {
      margin-bottom: 15px;
      background: #f9f9f9;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    /* Styling for trip titles */
    .trip-title {
      font-size: 1.2rem;
      font-weight: bold;
      color: #4a4e69;
    }

    /* Styling for trip details */
    .trip-details {
      color: #555;
      font-size: 1rem;
    }

    /* Countdown timer styling */
    .countdown {
      font-size: 0.9rem;
      color: #778da9;
      margin-top: 10px;
    }

    /* Styling for profile and trip sections */
    .profile-info {
      background: #f9f9f9;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    /* Styling for email label in profile section */
    .email-label {
      font-weight: bold;
      color: #4a4e69;
      font-family: 'Trebuchet MS', sans-serif;
      flex: 0 0 30%;
    }

    /* Styling for email value in profile section */
    .email-value {
      font-family: 'Arial', sans-serif;
      color: #555;
      flex: 1;
      text-align: left;
    }

    /* Button layout styling */
    .btn-layout {
      margin-top: 20px;
      margin-bottom: 20px;
    }

    /* Button secondary styling */
    .btn-second {
      background-color: #6c757d;
      color: #f8f9fa;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      text-align: center;
    }

    /* Button hover effect */
    .btn-second:hover {
      background-color: #9a8c98;
      color: black;
    }

    /* Button active effect */
    .btn-second:active {
      background-color: transparent;
      color: inherit;
    }

    /* Body and html layout */
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    /* Main content layout */
    main {
      flex: 1;
    }

    /* Footer layout */
    footer {
      padding: 20px 0;
      text-align: center;
    }

    /* Modal center styling */
    .modal-center {
      display: flex !important;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }

    /* Status tag styling */
    .status {
      font-weight: bold;
      padding: 5px 10px;
      border-radius: 5px;
      display: inline-block;
      font-size: 0.95rem;
      margin-top: 10px;
    }

    /* Pending status styling */
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    /* Confirmed status styling */
    .status-confirmed {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    /* Cancelled status styling */
    .status-cancelled {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    /* Current status styling */
    .status-current {
      background-color: #9a8c98;
      color: #7a5475;
      border: 1px solid #917d8e;
    }

    /* Past status styling */
    .status-past {
      background-color: #9a8c98;
      color: #7a5475;
      border: 1px solid #917d8e;
    }

    /* Media query for smaller screens */
    @media (max-width: 768px) {
      .info-container {
        flex-direction: column;
      }
      .profile-info,
      .upcoming-trips {
        width: 100%;
      }
    }

    /* Trip section layout for responsive design */
    .trip-sections {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      max-width: 1200px;
    }
    
  </style>
</head>

<body>
  <!-- Navigation bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="qwigley-regular navbar-brand" href="home.php">Dreamscape Destinations</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <!-- Home link -->
          <li class="nav-item">
            <a class="nav-link" href="home.php">
              <span class="material-symbols-outlined">home</span> Home
            </a>
          </li>
          <!-- Trips link -->
          <li class="nav-item">
            <a class="nav-link" href="trips.php">
              <span class="material-symbols-outlined">map</span> Trips
            </a>
          </li>
          <!-- About Us link -->
          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">
              <span class="material-symbols-outlined">info</span> About Us
            </a>
          </li>
          <!-- User Dashboard and Logout links for logged-in users -->
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link active" href="dashboard.php">
                <span class="material-symbols-outlined">account_circle</span> Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">
                <span class="material-symbols-outlined">logout</span> Logout
              </a>
            </li>
          <?php else: ?>
            <!-- Login/Sign-up link for guests -->
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

  <!-- Display success message if available -->
  <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success" role="alert">
          <?php echo htmlspecialchars($_SESSION['success_message']); ?>
      </div>
      <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <!-- Main content area -->
  <main class="container my-3">
    <!-- Welcome message for the user -->
    <h1 class="section-title">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    <div class="dashboard-container">
      <div class="info-container">
        <!-- Profile Info Section -->
        <div class="profile-info">
          <h3 class="card-title align">Profile Information</h3>
          <!-- Display Username -->
          <div class="email-row">
            <span class="email-label">Username:</span>
            <span class="email-value"><?php echo htmlspecialchars($user['username']); ?></span>
          </div>
          <!-- Display Email -->
          <div class="email-row">
            <span class="email-label">Email:</span>
            <span class="email-value"><?php echo htmlspecialchars($user['email']); ?></span>
          </div>
          <!-- Button to open modal for editing profile -->
          <a href="#" class="btn btn-theme btn-layout" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</a>
          
          <!-- Modal for Editing Profile -->
          <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title card-title" id="editProfileModalLabel">Edit Profile</h5>
                </div>
                <!-- Form for editing profile -->
                <form action="edit-profile.php" method="POST" id="profileForm">
                  <div class="modal-body">
                    <!-- Input for Username -->
                    <div class="mb-3">
                      <label for="username" class="form-label">Username</label>
                      <input type="text" class="form-control" name="username" id="username" required value="<?php echo htmlspecialchars($user['username']); ?>">
                    </div>
                    <!-- Input for Current Password -->
                    <div class="mb-3">
                      <label for="current_password" class="form-label">Current Password</label>
                      <input type="password" class="form-control" name="current_password" id="current_password" required>
                    </div>
                    <!-- Input for New Password -->
                    <div class="mb-3">
                      <label for="new_password" class="form-label">New Password</label>
                      <input type="password" class="form-control" name="new_password" id="new_password">
                    </div>
                    <!-- Input to Confirm New Password -->
                    <div class="mb-3">
                      <label for="confirm_password" class="form-label">Confirm New Password</label>
                      <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                    </div>
                    <!-- Display error messages if any -->
                    <div id="message-container">
                      <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; ?></div>
                        <?php unset($_SESSION['error_message']); ?>
                      <?php endif; ?>
                    </div>
                  </div>
                  <!-- Modal footer with Cancel and Save buttons -->
                  <div class="modal-footer">
                    <button type="button" class="btn btn-second" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_profile" class="btn btn-theme">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>



    <!-- Upcoming trips section -->
    <div class="dashboard-container">
      <h3 class="card-title align">Your Upcoming Trips</h3>
      <?php if ($upcomingTrips->num_rows > 0): ?>
          <ul>
              <?php while ($trip = $upcomingTrips->fetch_assoc()): ?>
                  <?php if ($trip['status'] != 'cancelled'): ?>
                  <li class="trip-item">
                      <div class="trip-title">
                          <!-- Display trip title and location -->
                          <?php echo htmlspecialchars($trip['title']) . " in " . htmlspecialchars($trip['location']); ?>
                      </div>
                      <div class="trip-details">
                          <!-- Display start and end dates, passengers, and class -->
                          From <?php echo date('F j, Y', strtotime($trip['start_date'])); ?> to <?php echo date('F j, Y', strtotime($trip['end_date'])); ?><br>
                          <?php echo $trip['passengers']; ?> Passengers<br>
                          <?php echo ucfirst($trip['class']); ?> 
                      </div>
                      
                      <?php
                          $statusClass = '';
                          $statusLabel = '';

                          // Determine trip status label and style based on status
                          switch ($trip['status']) {
                              case 'pending':
                                  $statusClass = 'status-pending';
                                  $statusLabel = 'Pending';
                                  break;
                              case 'confirmed':
                                  $statusClass = 'status-confirmed';
                                  $statusLabel = 'Confirmed';
                                  break;
                              default:
                                  $statusClass = '';
                                  $statusLabel = ucfirst($trip['status']);
                          }
                      ?>

                      <!-- Display trip status -->
                      <div class="status <?php echo $statusClass; ?>">
                          <?php echo $statusLabel; ?>
                      </div>

                      <div class="countdown" id="countdown-<?php echo $trip['booking_id']; ?>">
                      </div>
                      <script>
                      (function() {
                          var targetDate = new Date("<?php echo date('Y-m-d\TH:i:s', strtotime($trip['start_date'])); ?>").getTime();
                          var bookingId = "<?php echo $trip['booking_id']; ?>";

                          // Countdown timer logic
                          var intervalId = setInterval(function() {
                              var now = new Date().getTime();
                              var timeRemaining = targetDate - now;

                              var days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                              var hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                              var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                              var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                              var countdownElem = document.getElementById("countdown-" + bookingId);
                              if (timeRemaining >= 0) {
                                  countdownElem.innerHTML =
                                      "Countdown: " + days + " days " + hours + " hours " + minutes + " minutes " + seconds + " seconds";
                              } else {
                                  clearInterval(intervalId);
                                  countdownElem.innerHTML = "Trip started!";
                              }
                          }, 1000);
                      })();
                      </script>

                      <!-- Edit Trip button (only for pending trips) -->
                      <?php if ($trip['status'] == 'pending'): ?>
                          <button type="button" class="btn btn-theme btn-layout" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $trip['booking_id']; ?>">
                              Edit Trip
                          </button>

                          <!-- Edit Trip Modal -->
                          <div class="modal fade" id="editModal<?php echo $trip['booking_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $trip['booking_id']; ?>" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <h5 class="modal-title card-title" id="editModalLabel<?php echo $trip['booking_id']; ?>">Edit Trip Details</h5>
                                      </div>
                                      <form method="POST" action="edit-booking.php" id="editForm<?php echo $trip['booking_id']; ?>">
                                          <input type="hidden" name="booking_id" value="<?php echo $trip['booking_id']; ?>">
                                          <input type="hidden" id="base_price<?php echo $trip['booking_id']; ?>" value="<?php echo $trip['price']; ?>">

                                          <div class="modal-body">
                                              <!-- Start Date input -->
                                              <div class="mb-3">
                                                  <label for="start_date<?php echo $trip['booking_id']; ?>" class="form-label">Start Date</label>
                                                  <input type="date" class="form-control" name="start_date" id="start_date<?php echo $trip['booking_id']; ?>" value="<?php echo date('Y-m-d', strtotime($trip['start_date'])); ?>" required>
                                              </div>

                                              <!-- End Date input -->
                                              <div class="mb-3">
                                                  <label for="end_date<?php echo $trip['booking_id']; ?>" class="form-label">End Date</label>
                                                  <input type="date" class="form-control" name="end_date" id="end_date<?php echo $trip['booking_id']; ?>" value="<?php echo date('Y-m-d', strtotime($trip['end_date'])); ?>" required readonly>
                                              </div>

                                              <!-- Passengers input -->
                                              <div class="mb-3">
                                                  <label for="passengers<?php echo $trip['booking_id']; ?>" class="form-label">Number of Passengers</label>
                                                  <input type="number" class="form-control" name="passengers" value="<?php echo isset($trip['passengers']) ? $trip['passengers'] : 1; ?>" min="1" max="10" required id="passenger_count<?php echo $trip['booking_id']; ?>">
                                              </div>

                                              <!-- Class selection -->
                                              <div class="mb-3">
                                                  <label for="class<?php echo $trip['booking_id']; ?>" class="form-label">Class</label>
                                                  <select name="class" class="form-control" required id="class_select<?php echo $trip['booking_id']; ?>">
                                                      <option value="economy" <?php echo $trip['class'] == 'economy' ? 'selected' : ''; ?>>Economy</option>
                                                      <option value="business" <?php echo $trip['class'] == 'business' ? 'selected' : ''; ?>>Business</option>
                                                      <option value="first-class" <?php echo $trip['class'] == 'first-class' ? 'selected' : ''; ?>>First-Class</option>
                                                  </select>
                                              </div>

                                              <!-- Display total price -->
                                              <div class="mb-3">
                                                  <label class="form-label">Total Price</label>
                                                  <div id="totalPrice<?php echo $trip['booking_id']; ?>" class="form-control" style="background-color: #e9ecef; height: 38px; display: flex; align-items: center;">
                                                      <?php echo number_format($trip['price'], 2); ?>
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-second" data-bs-dismiss="modal">Cancel</button>
                                              <button type="submit" class="btn btn-theme">Save Changes</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>
                      <?php endif; ?>

                      <script>
                      document.addEventListener('DOMContentLoaded', function() {
                          const modals = document.querySelectorAll('[id^="editModal"]');
                          
                          modals.forEach(function(modal) {
                              const bookingId = modal.id.split('editModal')[1]; // Extract booking ID

                              const form = document.getElementById('editForm' + bookingId);
                              const classSelect = document.getElementById('class_select' + bookingId);
                              const passengerCount = document.getElementById('passenger_count' + bookingId);
                              const basePriceElement = document.getElementById('base_price' + bookingId);
                              const startDateInput = document.getElementById('start_date' + bookingId);
                              const endDateInput = document.getElementById('end_date' + bookingId);
                              
                              // Check if elements exist, if not skip processing for this booking
                              if (!form || !classSelect || !passengerCount || !basePriceElement || !startDateInput || !endDateInput) {
                                  console.error('Missing elements for booking ID:', bookingId);
                                  return; // Skip this modal if required elements are missing
                              }

                              // Form validation before submission
                              form.addEventListener('submit', function(event) {
                                  let isValid = true;

                                  // Validate that the start date is before the end date
                                  const startDate = new Date(startDateInput.value);
                                  const endDate = new Date(endDateInput.value);
                                  if (startDate >= endDate) {
                                      alert("The start date must be before the end date.");
                                      isValid = false;
                                  }

                                  // Validate that the number of passengers is valid (e.g., greater than 0)
                                  const passengers = parseInt(passengerCount.value, 10);
                                  if (isNaN(passengers) || passengers < 1 || passengers > 10) {
                                      alert("The number of passengers must be between 1 and 10.");
                                      isValid = false;
                                  }

                                  if (!isValid) {
                                      event.preventDefault(); // Prevent form submission if invalid
                                  }
                              });

                              // Update price function
                              const basePrice = parseFloat(basePriceElement.value);
                              const totalPrice = document.getElementById('totalPrice' + bookingId);

                              function updatePrice() {
                                  const passengers = parseInt(passengerCount.value, 10);
                                  let classMultiplier = 0;

                                  if (classSelect.value === 'business') {
                                      classMultiplier = 100; // Business class adds $100 to the price
                                  } else if (classSelect.value === 'first-class') {
                                      classMultiplier = 200; // First-class adds $200 to the price
                                  } else {
                                      classMultiplier = 0; // Economy doesn't add anything extra
                                  }

                                  const total = (basePrice + classMultiplier) * passengers;
                                  totalPrice.innerText = '$' + total.toFixed(2); // Update the price displayed
                              }

                              // Update price on class or passenger change
                              classSelect.addEventListener('change', updatePrice);
                              passengerCount.addEventListener('input', updatePrice);

                              // Initialize the price on page load
                              updatePrice();

                              // Update end date based on start date
                              startDateInput.addEventListener('change', function() {
                                  const startDate = new Date(startDateInput.value);
                                  if (!isNaN(startDate)) {
                                      startDate.setDate(startDate.getDate() + 6); // Set end date 6 days after start date
                                      const year = startDate.getFullYear();
                                      const month = ('0' + (startDate.getMonth() + 1)).slice(-2);
                                      const day = ('0' + startDate.getDate()).slice(-2);
                                      endDateInput.value = `${year}-${month}-${day}`; // Set end date value
                                  }
                              });
                          });
                      });
                      </script>


                      <!-- Cancel Trip button (only for pending or confirmed trips) -->
                      <?php if ($trip['status'] == 'pending' || $trip['status'] == 'confirmed'): ?>
                          <button type="button" class="btn btn-theme btn-layout" data-bs-toggle="modal" data-bs-target="#cancelModal<?php echo $trip['booking_id']; ?>">
                              Cancel Trip
                          </button>

                          <!-- Cancel Trip Modal -->
                          <div class="modal fade" id="cancelModal<?php echo $trip['booking_id']; ?>" tabindex="-1" aria-labelledby="cancelModalLabel<?php echo $trip['booking_id']; ?>" aria-hidden="true">
                              <div class="modal-dialog modal-dialog-centered">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <h5 class="modal-title card-title" id="cancelModalLabel<?php echo $trip['booking_id']; ?>">Confirm Cancellation</h5>
                                      </div>
                                      <form method="POST" action="cancel-trip.php">
                                          <div class="modal-body">
                                              <input type="hidden" name="booking_id" value="<?php echo $trip['booking_id']; ?>">
                                              <p class="text-center mb-0">Are you sure you want to cancel your trip to <strong><?php echo htmlspecialchars($trip['title']); ?></strong>?</p>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
                                              <button type="submit" class="btn btn-theme">Yes</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>
                      <?php endif; ?>
                  </li>
                <?php endif; ?>  
              <?php endwhile; ?>
          </ul>
      <?php else: ?>
          <p>No upcoming trips.</p>
      <?php endif; ?>
    </div>

  <!-- Current Trips Section -->
  <div class="dashboard-container">
    <h3 class="card-title align">Your Current Trips</h3>
    <?php if ($currentTrips->num_rows > 0): ?>
      <ul>
        <?php while ($trip = $currentTrips->fetch_assoc()): ?>
          <?php if ($trip['status'] != 'cancelled'): ?>
            <li class="trip-item">
              <div class="trip-title">
                <?php echo htmlspecialchars($trip['title']) . " in " . htmlspecialchars($trip['location']); ?>
              </div>
              <div class="trip-details">
                From <?php echo date('F j, Y', strtotime($trip['start_date'])); ?> to <?php echo date('F j, Y', strtotime($trip['end_date'])); ?><br>
                <?php echo $trip['passengers']; ?> Passengers<br>
                <?php echo ucfirst($trip['class']); ?>
              </div>
              <div class="status status-current">
                Current
              </div>
            </li>
          <?php endif; ?>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p>No current trips.</p>
    <?php endif; ?>
  </div>

  <!-- Past Trips Section -->
  <div class="dashboard-container">
    <h3 class="card-title align">Your Past Trips</h3>
    <?php if ($pastTrips->num_rows > 0): ?>
      <ul>
        <?php while ($trip = $pastTrips->fetch_assoc()): ?>
          <?php if ($trip['status'] != 'cancelled'): ?>
            <li class="trip-item">
              <div class="trip-title">
                <?php echo htmlspecialchars($trip['title']) . " in " . htmlspecialchars($trip['location']); ?>
              </div>
              <div class="trip-details">
                From <?php echo date('F j, Y', strtotime($trip['start_date'])); ?> to <?php echo date('F j, Y', strtotime($trip['end_date'])); ?><br>
                <?php echo $trip['passengers']; ?> Passengers<br>
                <?php echo ucfirst($trip['class']); ?>
              </div>
              <div class="status status-past">
                Past
              </div>
            </li>
          <?php endif; ?>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p>No past trips.</p>
    <?php endif; ?>
  </div>
</div>
</div>
</div>

</main>

<!-- Footer -->
<footer>
  <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<!-- Show modal if profile update -->
<script>
<?php if (isset($_SESSION['show_modal'])): ?>
  var modal = new bootstrap.Modal(document.getElementById('editProfileModal'));
  modal.show();
<?php unset($_SESSION['show_modal']); endif; ?>
</script>

</body>
</html>