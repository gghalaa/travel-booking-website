<?php
// Start session first
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get selected trip ID from the form or set to null (now after session_start)
$selectedTripId = $_POST['trip_id'] ?? null;

// Initialize DB connection
include 'db_config.php';

// Initialize confirmation page variables
$showConfirmation = false;
$totalAmount = '';
$destination = '';
$checkin = '';
$checkout = '';
$booking_id = '';

// Check if booking was successful and show confirmation
if (isset($_SESSION['booking_success']) && $_SESSION['booking_success']) {
    $showConfirmation = true;
    $totalAmount = $_SESSION['total_amount'] ?? '';
    $destination = $_SESSION['trip_location'] ?? '';
    $checkin = $_SESSION['checkin'] ?? '';
    $checkout = $_SESSION['checkout'] ?? '';
    $booking_id = $_SESSION['booking_id'] ?? '';

    // Clear session variables after displaying confirmation
    unset($_SESSION['booking_success'], $_SESSION['total_amount'], $_SESSION['trip_location'], $_SESSION['checkin'], $_SESSION['checkout'], $_SESSION['booking_id']);
}

// Fetch trips for dropdown in the booking form
$trips_result = $conn->query("SELECT trip_id, title, location FROM trips");
$trips = $trips_result ? $trips_result->fetch_all(MYSQLI_ASSOC) : [];

// Handle booking form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['book_trip'])) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['login_required'] = true;
        header("Location: home.php");
        exit();
    }

    // Get user input from the booking form
    $user_id = $_SESSION['user_id'];
    $trip_id = intval($_POST['trip_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $passengers = intval($_POST['passengers']);
    $class = trim($_POST['class']);
    $valid_classes = ['economy', 'business', 'first-class'];
    $current_date = date('Y-m-d');

    // Check if the selected class is valid
    if (!in_array($class, $valid_classes)) {
        echo '<script> 
                alert("Invalid class selected.");
                window.location.href = "home.php"; 
              </script>';
        exit();
    }

    // Prevent booking in the past
    if ($start_date < $current_date) {
        $_SESSION['error_message'] = "The start date cannot be in the past. Please choose a valid date.";
        header("Location: home.php");
        exit();
    }

    // Check for overlapping bookings
    $overlap_query = "SELECT * FROM bookings 
    WHERE user_id = ? 
    AND status IN ('confirmed', 'pending') 
    AND NOT (end_date < ? OR start_date > ?)";

    $stmt = $conn->prepare($overlap_query);
    $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    $stmt->execute();
    $overlap_result = $stmt->get_result();

    // If overlap found, show error and redirect
    if ($overlap_result->num_rows > 0) {
        $_SESSION['error_message'] = "You already have a booking in the selected time frame. Please choose different dates.";
        header("Location: home.php");
        exit();
    }

    // Fetch trip price and details
    $price_stmt = $conn->prepare("SELECT price, title, location FROM trips WHERE trip_id = ?");
    $price_stmt->bind_param("i", $trip_id);
    $price_stmt->execute();
    $price_stmt->bind_result($trip_price, $trip_title, $trip_location);
    if (!$price_stmt->fetch()) {
        echo '<script> 
                alert("Trip not found or price unavailable.");
                window.location.href = "home.php"; 
              </script>';
        exit();
    }
    $price_stmt->close();

    // Calculate the total amount based on class and number of passengers
    $amount = $trip_price * $passengers;
    if ($class == 'business') {
        $amount += 100 * $passengers;
    } elseif ($class == 'first-class') {
        $amount += 200 * $passengers;
    }

    $_SESSION['total_amount'] = $amount;

    // Set booking details
    $booking_date = date('Y-m-d');
    $status = 'pending';

    // Insert booking into the database
    $stmt = $conn->prepare("INSERT INTO bookings 
    (user_id, trip_id, booking_date, status, start_date, end_date, passengers, class, amount) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssisd", $user_id, $trip_id, $booking_date, $status, $start_date, $end_date, $passengers, $class, $amount);
    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        $_SESSION['booking_success'] = true;
        $_SESSION['trip_id'] = $trip_id;
        $_SESSION['trip_title'] = $trip_title;
        $_SESSION['trip_location'] = $trip_location;
        $_SESSION['checkin'] = $start_date;
        $_SESSION['checkout'] = $end_date;
        $_SESSION['passengers'] = $passengers;
        $_SESSION['class'] = $class;
        $_SESSION['booking_id'] = $booking_id;
        header("Location: confirmation.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta and title -->  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dreamscape Destinations</title>

  <!-- Styles and fonts -->  
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    /* Button styles */
    .btn-second {
      background-color: #6c757d;
      color: #f8f9fa;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      text-align: center;
    }

    .btn-second:hover {
      background-color: #9a8c98;
      color: black;
    }

    .btn-second:active {
      background-color: transparent;
      color: inherit; 
    }

    /* Carousel styles */
    #homeCarousel {
      max-height: 500px;
      overflow: hidden;
    }

    .carousel-item img {
      width: 100%;
      height: 500px;
      object-fit: cover;
    }

    .carousel-caption {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      background-color: rgba(0, 0, 0, 0.6);
      padding: 20px 30px;
      border-radius: 8px;
      width: 80%;
      max-width: 900px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .carousel-caption h5 {
      font-size: 2.5rem;
      font-weight: bold;
      color: #fff;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
      margin-bottom: 5px;
    }

    .carousel-caption p {
      font-size: 1.3rem;
      color: #fff;
      margin-bottom: 10px;
    }

    .carsouel-caption a.btn-custom {
      margin-top: 5px;
    }

    .carousel-indicators [data-bs-target] {
      background-color: #fff;
    }

    /* Booking form styles */
    .booking-form {
      background-color: #f8f9fa;
      padding: 50px 0;
      border-top: 2px solid #4a4e69;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .booking-form .form-label {
      font-weight: bold;
      color: #4a4e69;
      font-family: 'Trebuchet MS', sans-serif;
    }

    .booking-form .form-select:focus,
    .booking-form .form-control:focus {
      border-radius: 8px;
      padding: 12px;
      border: 1px solid #ced4da;
    }

    .booking-form .form-select,
    .booking-form .form-control {
      border-color: #4a4e69;
      box-shadow: 0 0 5px rgba(74, 78, 105, 0.5);
    }

    /* Responsive styles */
    @media (max-width: 991px) {
      .carousel-caption h5 {
        font-size: 1.8rem;
      }
      .carousel-caption p,
      .carousel-caption a.btn-custom {
        font-size: 1rem;
      }
    }

    @media (max-width: 768px) {
      .carousel-caption {
        width: 90%;
        padding: 15px 20px;
      }

      .carousel-caption h5 {
        font-size: 1.5rem;
      }

      .carousel-caption p {
        font-size: 1rem;
      }

      .carousel-caption a.btn-custom {
        font-size: 1rem;
        padding: 8px 16px;
      }

      .carousel-item img {
        height: 300px;
      }
    }

    @media (max-width: 480px) {
      .carousel-caption {
        width: 95%;
        padding: 12px 15px;
      }

      .carousel-caption h5 {
        font-size: 1.2rem;
      }

      .carousel-caption p {
        font-size: 0.9rem;
      }

      .carousel-caption a.btn-custom {
        font-size: 0.9rem;
        padding: 6px 12px;
      }

      .carousel-item img {
        height: 250px;
      }
    }
  </style>
</head>

<body>
  <!-- Error Modal -->
  <?php if (isset($_SESSION['error_message'])): ?>
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title card-title" id="errorModalLabel">Booking Error</h5>
          </div>
          <div class="modal-body">
            <?= $_SESSION['error_message']; ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-theme" data-bs-dismiss="modal" id="closeModalButton">Close</button>
          </div>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const errorModalEl = document.getElementById('errorModal');
        if (errorModalEl) {
          const errorModal = new bootstrap.Modal(errorModalEl);
          errorModal.show();
          const closeBtn = document.getElementById('closeModalButton');
          if (closeBtn) {
            closeBtn.addEventListener('click', function () {
              errorModal.hide();
            });
          }
        }
      });
    </script>
    <?php unset($_SESSION['error_message']); ?>
  <?php endif; ?>

  <!-- Login Required Modal -->
  <?php if (isset($_SESSION['login_required'])): ?>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form method="POST" action="account.php">
            <div class="modal-header">
              <h5 class="card-title modal-title" id="loginModalLabel">Please Log In</h5>
            </div>
            <div class="modal-body">
              You need to be logged in to perform this action. Would you like to log in now?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-second" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="confirm_login" class="btn btn-theme">Log In</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const loginModalEl = document.getElementById('loginModal');
        const loginModal = new bootstrap.Modal(loginModalEl);
        loginModal.show();
      });
    </script>
    <?php unset($_SESSION['login_required']); ?>
  <?php endif; ?>

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
          <li class="nav-item">
            <a class="nav-link active" href="home.php">
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

          <!-- If user is logged in, show Dashboard and Logout options -->
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

          <!-- If user is not logged in, show Login/Sign Up option -->
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

  <!-- Booking Section -->
  <section class="booking-form">
    <div class="container">
      <h2 class="section-title text-center mb-4">Book Your Dream Destination</h2>
      <h2 class="caption">Your 7 Day Adventure Starts Here...</h2>
      
      <!-- Booking Form -->
      <form action="" method="POST" id="booking-form">
        <input type="hidden" name="book_trip" value="1" />
        
        <div class="row">
          <!-- Destination Selection -->
          <div class="col-md-4">
            <label for="destination" class="form-label">Destination</label>
            <select id="destination" name="trip_id" class="form-select" required>
              <option value="" disabled <?= $selectedTripId ? '' : 'selected' ?>>Select Destination</option>
              <?php foreach ($trips as $trip): ?>
                <option value="<?= htmlspecialchars($trip['trip_id']) ?>"
                  <?= $selectedTripId == $trip['trip_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($trip['title']) ?> (<?= htmlspecialchars($trip['location']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Check-in Date -->
          <div class="col-md-4">
            <label for="check-in" class="form-label">Check-in Date</label>
            <input type="date" id="check-in" name="start_date" class="form-control" required />
          </div>

          <!-- Check-out Date -->
          <div class="col-md-4">
            <label for="check-out" class="form-label">Check-out Date</label>
            <input type="date" id="check-out" name="end_date" class="form-control" required />
          </div>
        </div>

        <div class="row mt-3">
          <!-- Number of Passengers -->
          <div class="col-md-4">
            <label for="passengers" class="form-label">Number of Passengers</label>
            <input type="number" id="passengers" name="passengers" class="form-control" min="1" max="10" required />
          </div>

          <!-- Class Selection -->
          <div class="col-md-4">
            <label for="class" class="form-label">Class</label>
            <select id="class" name="class" class="form-select" required>
              <option value="" disabled selected>Select Class</option>
              <option value="economy">Economy Class</option>
              <option value="business">Business Class</option>
              <option value="first-class">First Class</option>
            </select>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-theme btn-custom">Book Now</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Carousel -->
  <header id="homeCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
  </div>
  <div class="carousel-inner">
    <!-- Carousel item for Bali Adventure -->
    <div class="carousel-item active">
      <img src="assets/images/bali.jpg" alt="A scenic beach in Bali, Indonesia" class="d-block w-100" />
      <div class="carousel-caption">
        <h5 class="card-title">Bali Adventure</h5>
        <p>Beach + Culture Tour - Starting from $1,200.00</p>
        <a href="trips.php" class="btn btn-theme btn-custom">View All Trips</a>
      </div>
    </div>
    <!-- Carousel item for Paris Getaway -->
    <div class="carousel-item">
      <img src="assets/images/paris.jpg" alt="Eiffel Tower at sunset in Paris" class="d-block w-100" />
      <div class="carousel-caption">
        <h5 class="card-title">Paris Getaway</h5>
        <p>Experience the city - Starting from $1,400.00</p>
        <a href="trips.php" class="btn btn-theme btn-custom">View All Trips</a>
      </div>
    </div>
    <!-- Carousel item for Dubai Luxury Tour -->
    <div class="carousel-item">
      <img src="assets/images/dubai.jpg" alt="Skyscrapers and desert view in Dubai" class="d-block w-100" />
      <div class="carousel-caption">
        <h5 class="card-title">Dubai Luxury Tour</h5>
        <p>Desert + Luxury Shopping - Starting from $2,000.00</p>
        <a href="trips.php" class="btn btn-theme btn-custom">View All Trips</a>
      </div>
    </div>
    <!-- Carousel item for Tokyo Highlights -->
    <div class="carousel-item">
      <img src="assets/images/tokyo.jpg" alt="Night lights and skyline in Tokyo" class="d-block w-100" />
      <div class="carousel-caption">
        <h5 class="card-title">Tokyo Highlights</h5>
        <p>Modern Japan Tour - Starting from $1,800.00</p>
        <a href="trips.php" class="btn btn-theme btn-custom">View All Trips</a>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
  </button>
</header>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Auto-fill check-out date based on check-in date
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');

    if (checkInInput && checkOutInput) {
      checkInInput.addEventListener('change', function () {
        const checkInDate = new Date(this.value);
        if (!isNaN(checkInDate.getTime())) {
          const checkOutDate = new Date(checkInDate);
          checkOutDate.setDate(checkOutDate.getDate() + 6);
          checkOutInput.value = checkOutDate.toISOString().split('T')[0];
        }
      });
    }
  });

  // Form validation
  document.getElementById('booking-form').addEventListener('submit', function (event) {
    const checkInDate = new Date(document.getElementById('check-in').value);
    const checkOutDate = new Date(document.getElementById('check-out').value);
    const passengers = document.getElementById('passengers').value;
    const tripSelect = document.getElementById('destination');
    
    // Check if destination is selected
    if (tripSelect.value === '') {
      event.preventDefault();
      alert('Please select a destination.');
      return;
    }

    // Check if check-in date is valid
    if (!checkInDate || isNaN(checkInDate.getTime())) {
      event.preventDefault();
      alert('Please select a valid check-in date.');
      return;
    }

    // Check if check-out date is valid and later than check-in date
    if (!checkOutDate || isNaN(checkOutDate.getTime())) {
      event.preventDefault();
      alert('Please select a valid check-out date.');
      return;
    }

    if (checkInDate >= checkOutDate) {
      event.preventDefault();
      alert('Check-out date must be later than check-in date.');
      return;
    }

    // Check number of passengers
    if (passengers < 1 || passengers > 10) {
      event.preventDefault();
      alert('Number of passengers must be between 1 and 10.');
      return;
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Auto-fill check-out date based on check-in date
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');

    if (checkInInput && checkOutInput) {
      checkInInput.addEventListener('change', function () {
        const checkInDate = new Date(this.value);
        if (!isNaN(checkInDate.getTime())) {
          const checkOutDate = new Date(checkInDate);
          checkOutDate.setDate(checkOutDate.getDate() + 6);
          checkOutInput.value = checkOutDate.toISOString().split('T')[0];
        }
      });
    }
  });
</script>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    // Set the selected destination in the trip dropdown based on the URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const destination = urlParams.get('destination');

    if (destination) {
      const select = document.querySelector('select[name="trip_id"]');
      if (select) {
        const options = select.options;
        for (let i = 0; i < options.length; i++) {
          if (options[i].textContent.includes(destination)) {
            select.selectedIndex = i;
            break;
          }
        }
      }
    }
  });
</script>

<footer>
  <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>