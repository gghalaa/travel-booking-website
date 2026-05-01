<?php
session_start(); // Start the session to track user data

// Initialize DB connection
include 'db_config.php';

// Check if a trip_id is passed in the form
if (isset($_POST['trip_id'])) {
    $trip_id = (int) $_POST['trip_id']; // Sanitize and cast the trip_id to an integer

    // SQL query to fetch trip details by trip_id
    $sql = "SELECT * FROM trips WHERE trip_id = ?";
    $stmt = $conn->prepare($sql); // Prepare the SQL query
    $stmt->bind_param("i", $trip_id); // Bind the trip_id to the query
    $stmt->execute(); // Execute the query
    $result = $stmt->get_result(); // Get the result of the query

    // Check if the trip was found in the database
    if ($result->num_rows > 0) {
        $trip = $result->fetch_assoc(); // Fetch the trip details as an associative array
    } else {
        echo "<h2>Trip not found.</h2>"; // Show an error message if the trip is not found
        exit; // Stop the script if no trip is found
    }
    $stmt->close(); // Close the prepared statement
} else {
    echo "<h2>No trip ID received.</h2>"; // Show an error message if no trip_id is received
    exit; // Stop the script if no trip_id is received
}

// Define a set of images for each trip (carousel images)
$tripImages = [
    1 => ['assets/images/bali.jpg', 'assets/images/bali2.jpg', 'assets/images/bali3.jpg'],
    2 => ['assets/images/rome.jpg', 'assets/images/rome2.jpg', 'assets/images/rome3.jpg'],
    3 => ['assets/images/tokyo.jpg', 'assets/images/tokyo2.jpg', 'assets/images/tokyo3.jpg'],
    4 => ['assets/images/paris.jpg', 'assets/images/paris2.jpg', 'assets/images/paris3.jpg'],
    5 => ['assets/images/brazil.jpg', 'assets/images/brazil2.jpg', 'assets/images/brazil3.jpg'],
    6 => ['assets/images/newyork.jpg', 'assets/images/newyork2.jpg', 'assets/images/newyork3.jpg'],
    7 => ['assets/images/dubai.jpg', 'assets/images/dubai2.jpg', 'assets/images/dubai3.jpg'],
    8 => ['assets/images/sydney.jpg', 'assets/images/sydney2.jpg', 'assets/images/sydney3.jpg'],
    9 => ['assets/images/iceland.jpg', 'assets/images/iceland2.jpg', 'assets/images/iceland3.jpg'],
    10 => ['assets/images/athens.jpg', 'assets/images/athens2.jpg', 'assets/images/athens3.jpg'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($trip['title']) ?> - Dreamscape Destinations</title> <!-- Title of the page with trip name -->
    <link rel="stylesheet" href="styles.css" /> <!-- Link to the external CSS file -->
    <link rel="preconnect" href="https://fonts.googleapis.com" /> <!-- Preconnect for Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /> <!-- Preconnect for fonts.gstatic.com -->
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Viewport settings for responsive design -->
    <style>
        /* Carousel styling for images */
        .carousel-item img {
            width: 100%;
            height: 400px;
            object-fit: cover; /* Ensure image covers the area */
            border-radius: 10px; /* Round corners of the images */
        }

        .carousel-inner {
            width: 100%;
            height: 400px;
        }

        /* Styling for carousel indicators (navigation dots) */
        .carousel-indicators {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            margin: 0;
        }

        .carousel-indicators button {
            width: 20px;
            height: 8px;
            background-color: rgba(255, 255, 255, 0.5); /* Transparent background */
            border: none;
            border-radius: 2px; /* Rounded corners for buttons */
            transition: background-color 0.3s ease; /* Smooth transition on hover */
        }

        .carousel-indicators .active {
            background-color: #fff; /* Active indicator button color */
        }

        /* Section title styling */
        .section-title {
            margin-bottom: 50px;
            color: black;
            text-align: center;
            font-size: 2.5rem; /* Larger font size for section title */
        }

        .btn-size {
            margin-top: 50px;
        }

        /* Centering the carousel */
        .carousel {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container for layout and responsiveness */
        .container {
            max-width: 1200px;
            margin: 0 auto; /* Center content */
            padding: 0 1rem;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            body {
                font-size: 1.05rem; /* Slightly larger font for readability */
            }

            h2, h3, h4 {
                font-size: 1.3rem; /* Adjust heading sizes on mobile */
            }

            /* Adjust carousel image for mobile */
            .carousel-item img {
                height: auto;
                max-height: 250px; /* Limit image height */
                object-fit: contain; /* Ensure the image fits without cropping */
                border-radius: 6px;
            }

            .carousel-inner {
                height: auto;
            }
        }
    </style>
</head>

<body>
  <!-- Navigation Bar -->
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
            <a class="nav-link active" href="trips.php">
              <span class="material-symbols-outlined">map</span> Trips
            </a>
          </li>
          <!-- About Us link -->
          <li class="nav-item">
            <a class="nav-link" href="aboutus.php">
              <span class="material-symbols-outlined">info</span> About Us
            </a>
          </li>

          <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Dashboard link (if logged in) -->
            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">
                <span class="material-symbols-outlined">account_circle</span> Dashboard
              </a>
            </li>
            <!-- Logout link -->
            <li class="nav-item">
              <a class="nav-link" href="logout.php">
                <span class="material-symbols-outlined">logout</span> Logout
              </a>
            </li>
          <?php else: ?>
            <!-- Login/Sign Up link (if not logged in) -->
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

  <!-- Trip Details Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <!-- Trip title -->
      <h2 class="section-title"><?= htmlspecialchars($trip['title']) ?></h2>
      <div class="row align-items-center">

        <!-- Carousel for trip images -->
        <div class="col-lg-6 mb-4">
          <div id="tripCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
              <?php foreach ($tripImages[$trip_id] as $index => $image): ?>
                <button type="button" data-bs-target="#tripCarousel" data-bs-slide-to="<?= $index ?>" <?= $index === 0 ? 'class="active"' : '' ?>></button>
              <?php endforeach; ?>
            </div>
            <div class="carousel-inner">
              <?php foreach ($tripImages[$trip_id] as $index => $image): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                  <img src="<?= htmlspecialchars($image) ?>" alt="Trip Image <?= $index + 1 ?>" />
                </div>
              <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#tripCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#tripCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
          </div>
        </div>

        <!-- Trip overview, description, and itinerary -->
        <div class="col-lg-6">
          <h3>Trip Overview</h3>
          <p><?= htmlspecialchars($trip['description']) ?></p>

          <!-- Itinerary list -->
          <h4>Itinerary:</h4>
          <ul>
            <?php foreach (explode("\n", $trip['itinerary']) as $item): ?>
              <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>

          <!-- Pricing options -->
          <h4>Pricing:</h4>
          <ul>
            <li><strong>Economy Class:</strong> $<?= number_format($trip['price'], 2) ?> per person</li>
            <li><strong>Business Class:</strong> $<?= number_format($trip['price'] + 100, 2) ?> per person</li>
            <li><strong>First Class:</strong> $<?= number_format($trip['price'] + 200, 2) ?> per person</li>
          </ul>
        </div>

      </div>

      <!-- Booking button -->
      <div class="row justify-content-center mt-4">
        <div class="col-auto">
          <form action="home.php" method="post">
            <input type="hidden" name="trip_id" value="<?= htmlspecialchars($trip['trip_id']) ?>">
            <button type="submit" class="btn btn-theme btn-custom btn-size">Book Now</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer Section -->
  <footer>
    <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS for carousel functionality -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>