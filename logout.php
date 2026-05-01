<?php
session_start();

// Store the referring page URL to redirect back after cancellation
if (!isset($_SESSION['logout_referrer']) && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['logout_referrer'] = $_SERVER['HTTP_REFERER'];
}

// Process logout confirmation
if (isset($_POST['confirm_logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session entirely
    header("Location: home.php"); // Redirect to the home page after logout
    exit();
}

// Process cancellation and return to the referring page
if (isset($_POST['cancel_logout']) && isset($_SESSION['logout_referrer'])) {
    $returnTo = $_SESSION['logout_referrer']; // Retrieve the stored referrer URL
    unset($_SESSION['logout_referrer']); // Clear the referrer URL from session
    header("Location: $returnTo"); // Redirect to the referrer URL
    exit();
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive meta tag -->
  <title>Logout Confirmation</title>

  <!-- Link to external CSS file -->
  <link rel="stylesheet" href="styles.css">
  
  <!-- Preconnect to Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  
  <!-- Custom CSS for Cancel button style -->
  <style>
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
  </style>
</head>

<body>
  <!-- Logout Confirmation Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title card-title" id="logoutModalLabel">Confirm Logout</h5>
        </div>
        <div class="modal-body">
          Are you sure you want to log out?
        </div>
        <div class="modal-footer d-flex justify-content-end align-items-center">
          <button type="submit" name="cancel_logout" class="btn btn-second me-2">Cancel</button>
          <button type="submit" name="confirm_logout" class="btn btn-theme">Logout</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS to enable modal functionality -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- JavaScript to automatically show the logout modal -->
  <script>
    window.addEventListener('DOMContentLoaded', function () {
      var logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      logoutModal.show(); // Show the modal to confirm logout
    });
  </script>
</body>
</html>
