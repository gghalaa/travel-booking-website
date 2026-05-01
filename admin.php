<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for admin login
session_start();

// Redirect if user is not an admin
if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta and title -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - Dreamscape Destinations</title>

  <!-- External styles and fonts -->
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <!-- Inline styles for layout and responsiveness -->
  <style>
    html, body {
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    main {
      flex-grow: 1;
    }
    footer {
      margin-top: auto;
    }
    .admin-welcome {
      text-align: center;
      padding: 60px 20px;
    }
    .admin-welcome h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }
    .admin-welcome p {
      margin-bottom: 30px;
    }
    @media (max-width: 768px) {
      .navbar-brand {
        display: block;
        text-align: center;
      }
      .navbar-brand span {
        display: block;
      }
    }
  </style>
</head>
<body>

  <!-- Admin navigation bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="qwigley-regular navbar-brand" href="admin.php">
        Dreamscape Destinations <span>- Admin</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link active" href="admin.php">
              <span class="material-symbols-outlined">home</span> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage_trips.php">
              <span class="material-symbols-outlined">map</span> Manage Trips
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage_bookings.php">
              <span class="material-symbols-outlined">book</span> Manage Bookings
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage_users.php">
              <span class="material-symbols-outlined">person</span> Manage Users
            </a>
          </li>
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

  <!-- Welcome message -->
  <div class="admin-welcome">
    <h1 class="section-title">Welcome, Admin!</h1>
    <p class="caption">You have full control over: trips, bookings, and user management. Use the navigation above to get started.</p>
  </div>

  <!-- Footer -->
  <footer>
    <p class="mb-0 text-center"> Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
