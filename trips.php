<?php
session_start();

// Initialize DB connection
include 'db_config.php';

// Handle search functionality for trips
$search = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($search)) {
  $sql = "SELECT * FROM trips WHERE title LIKE ? OR location LIKE ?";
  $stmt = $conn->prepare($sql);
  $searchTerm = "%" . $search . "%";
  $stmt->bind_param("ss", $searchTerm, $searchTerm);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  // If no search, fetch all trips
  $sql = "SELECT * FROM trips";
  $result = $conn->query($sql);
}

// Fetch trips into array
$trips = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
  }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trips - Dreamscape Destinations</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    .card-body {
      padding: 20px;
    }
    .card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
    }
    .card-img-top {
      height: 250px;
      object-fit: cover;
    }
    footer {
      background-color: #4a4e69;
      color: white;
      padding: 20px;
    }
    .section-title {
      margin-bottom: 50px;
    }
    .btn-size {
      margin-top: 10px;
      padding: 8px 18px;
      font-size: 1.1rem;
      border-radius: 5px;
    }
  </style>
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="qwigley-regular navbar-brand" href="home.php">Dreamscape Destinations</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="home.php">
            <span class="material-symbols-outlined">home</span> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="trips.php">
            <span class="material-symbols-outlined">map</span> Trips
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="aboutus.php">
            <span class="material-symbols-outlined">info</span> About Us
          </a>
        </li>

        <!-- Check if the user is logged in -->
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


<!-- All trips section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="section-title">Explore All The Trips</h2>

    <!-- Search form for trips -->
    <form id="searchForm" method="get" class="d-flex justify-content-center mb-4" style="max-width: 600px; margin: 0 auto; ">
      <div style="position: relative; width: 100%;">
        <span class="material-symbols-outlined" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: gray;">
        search
        </span>
        <input type="text" name="search" class="form-control" placeholder="Search trips by title or location..." style="padding-left: 40px;" value="<?php echo htmlspecialchars($search); ?>">
      </div>
    </form>
    <script>
      if (window.location.search.includes('search=')) {
        const url = new URL(window.location);
        url.searchParams.delete('search');
        window.history.replaceState({}, document.title, url.pathname);
      }
    </script>

     <div class="row">
      <?php
      // Check if there are trips available
      if (count($trips) > 0) {
        foreach ($trips as $trip) {

          // Assign image path based on trip title
          $imagePath = '';
          switch ($trip['title']) {
            case 'Bali Adventure':
              $imagePath = 'bali.jpg';
              break;
            case 'Rome Exploration':
              $imagePath = 'rome.jpg';
              break;
            case 'Tokyo Highlights':
              $imagePath = 'tokyo.jpg';
              break;
            case 'Paris Getaway':
              $imagePath = 'paris.jpg';
              break;
            case 'Amazon Expedition':
              $imagePath = 'brazil.jpg';
              break;
            case 'New York Escape':
              $imagePath = 'newyork.jpg';
              break;
            case 'Dubai Luxury Tour':
              $imagePath = 'dubai.jpg';
              break;
            case 'Sydney Explorer':
              $imagePath = 'sydney.jpg';
              break;
            case 'Iceland Sighting':
              $imagePath = 'iceland.jpg';
              break;
            case 'Greece Discovery':
              $imagePath = 'athens.jpg';
              break;
          }

          // Display trip card with the selected image
          echo '
          <div class="col-md-4">
            <div class="card h-100">
              <img src="' . $imagePath . '" class="card-img-top" alt="' . $trip['title'] . '">
              <div class="card-body">
                <h5 class="card-title">' . $trip['title'] . '</h5>
                <p class="card-text">' . $trip['summary'] . '</p>
                <p class="text-muted">$' . number_format($trip['price'], 2) . ' – ' . $trip['location'] . '</p>
                <form action="trip-details.php" method="POST">
                  <input type="hidden" name="trip_id" value="' . $trip['trip_id'] . '">
                  <button type="submit" class="btn btn-theme btn-size">View Details</button>
                </form>
              </div>
            </div>
          </div>';
        }
      } else {
        echo '<p>No trips available at the moment.</p>';
      }
      ?>
    </div>
  </div>
</section>

<script>
  // Implement a search input with delay for submitting the form
  const searchInput = document.querySelector('#searchForm input[name="search"]');
  searchInput.addEventListener('input', function () {
    const form = document.getElementById('searchForm');
    clearTimeout(form._searchTimeout);
    form._searchTimeout = setTimeout(() => {
    form.submit();  // Submit after delay (0.5s)
    }, 500);
  });
</script>

<!-- Footer -->
<footer>
  <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>