<?php
require_once 'db_config.php';
session_start();

// Redirect to dashboard if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Signup form handling
    if (isset($_POST['signup-name'], $_POST['signup-email'], $_POST['signup-password'])) {
        $username = $_POST['signup-name'];
        $email = $_POST['signup-email'];
        $password = $_POST['signup-password'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Invalid email format.";
            header('Location: account.php');
            exit();
        }

        // Validate password length
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = "Password must be at least 6 characters long.";
            header('Location: account.php');
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "An account with this email already exists.";
            header('Location: account.php');
            exit();
        }

        // Insert new user into database
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("sss", $username, $password_hash, $email);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Account created successfully!";
                session_write_close();
                header('Location: account.php');
                exit();
            } else {
                throw new Exception("Error executing query: " . $stmt->error);
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: account.php');
            exit();
        }
    }

    // Login form handling
    if (isset($_POST['login-email'], $_POST['login-password'])) {
        $login_email = $_POST['login-email'];
        $login_password = $_POST['login-password'];

        // Validate email format
        if (!filter_var($login_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Invalid email format.";
            header('Location: account.php');
            exit();
        }

        try {
            // Find user by email
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            $stmt->bind_param("s", $login_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify password
                if (password_verify($login_password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['admin'] = ($_SESSION['email'] === 'admin@dreamscape.com');

                    session_write_close();

                    // Redirect based on user role
                    header('Location: ' . ($_SESSION['admin'] ? 'admin.php' : 'dashboard.php'));
                    exit();
                } else {
                    $_SESSION['error_message'] = "Invalid password.";
                    header('Location: account.php');
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "No user found with that email.";
                header('Location: account.php');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: account.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags for character set and viewport settings -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <!-- Page title -->
  <title>Account - Dreamscape Destinations</title>
  
  <!-- Link to external CSS stylesheet -->
  <link rel="stylesheet" href="assets/css/styles.css">
  
  <!-- Preconnect to Google Fonts for faster font loading -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  
  <!-- Internal CSS styles -->
  <style>
    /* Container for the form */
    .form-container {
      max-width: 450px;
      margin: 60px auto;
      background: white;
      padding: 50px;
      border-radius: 10px;
      box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
    }

    /* Title of the form */
    .form-title {
      text-align: center;
      margin-bottom: 25px;
      font-size: 1.8rem;
      font-weight: bold;
      color: #4a4e69;
      font-family: 'Trebuchet MS', sans-serif;
    }

    /* Label styling */
    .form-label {
      font-family: 'Trebuchet MS', sans-serif;
      color: #4a4e69;
      font-weight: bold;
    }

    /* Button style for the toggle between login and signup */
    .btn-toggle {
      display: block;
      margin: 10px auto 25px;
      font-size: 0.9rem;
      background: none;
      border: none;
      color: #9a8c98;
      text-decoration: underline;
      cursor: pointer;
    }

    /* Ensuring full height for HTML and body */
    html, body {
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    /* Main section that grows to take available space */
    main {
      flex-grow: 1;
    }

    /* Footer at the bottom of the page */
    footer {
      margin-top: auto;
    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="qwigley-regular navbar-brand" href="home.php">Dreamscape Destinations</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <!-- Navigation links for Home, Trips, About Us -->
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
          <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Display Dashboard and Logout if the user is logged in -->
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
            <!-- Display Login/Sign Up if the user is not logged in -->
            <li class="nav-item">
              <a class="nav-link active" href="account.php">
                <span class="material-symbols-outlined">login</span> Login / Sign Up
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Login and Signup Forms Section -->
  <div class="form-container" id="form-container">
    <h2 class="form-title" id="form-title">Login to Your Account</h2>

    <?php
    // Display error messages if they exist
    if (isset($_SESSION['error_message'])) {
      if (is_array($_SESSION['error_message'])) {
        foreach ($_SESSION['error_message'] as $message) {
          echo "<div class='alert alert-danger'>{$message}</div>";
        }
      } else {
        echo "<div class='alert alert-danger'>{$_SESSION['error_message']}</div>";
      }
      unset($_SESSION['error_message']);
    }

    // Display success message if it exists
    if (isset($_SESSION['success_message'])) {
      echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>";
      unset($_SESSION['success_message']);
    }
    ?>

    <!-- Login Form -->
    <form id="login-form" action="account.php" method="POST" style="display: block;">
      <div class="mb-3">
        <label for="login-email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="login-email" name="login-email" required />
      </div>
      <div class="mb-3">
        <label for="login-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="login-password" name="login-password" required />
      </div>
      <button type="submit" class="btn btn-theme w-100">Login</button>
    </form>

    <!-- Signup Form (Initially hidden) -->
    <form id="signup-form" action="account.php" method="POST" style="display: none;">
      <div class="mb-3">
        <label for="signup-name" class="form-label">Name</label>
        <input type="text" class="form-control" id="signup-name" name="signup-name" required />
      </div>
      <div class="mb-3">
        <label for="signup-email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="signup-email" name="signup-email" required />
      </div>
      <div class="mb-3">
        <label for="signup-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="signup-password" name="signup-password" required />
      </div>
      <button type="submit" class="btn btn-theme w-100">Sign Up</button>
    </form>

    <!-- Button to toggle between Login and Signup forms -->
    <button id="toggle-form" class="btn-toggle" onclick="toggleForms()">Don't have an account? Sign up</button>
  </div>

  <!-- Footer Section -->
  <footer>
    <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- JavaScript to toggle between Login and Signup forms -->
  <script>
    function toggleForms() {
      const loginForm = document.getElementById("login-form");
      const signupForm = document.getElementById("signup-form");
      const formTitle = document.getElementById("form-title");
      const toggleButton = document.getElementById("toggle-form");

      // Toggle visibility between Login and Signup forms
      if (loginForm.style.display === "none") {
        loginForm.style.display = "block";
        signupForm.style.display = "none";
        formTitle.textContent = "Login to Your Account";
        toggleButton.textContent = "Don't have an account? Signup";
      } else {
        loginForm.style.display = "none";
        signupForm.style.display = "block";
        formTitle.textContent = "Create an Account";
        toggleButton.textContent = "Already have an account? Login";
      }
    }
  </script>

  <!-- Bootstrap JavaScript for responsive features -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>