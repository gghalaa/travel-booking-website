<?php
// Start the session
session_start();

// Initialize DB connection
include 'db_config.php';

// Handle form submissions when the method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the action is set in the form
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // If the action is 'add', add a new user
        if ($action === 'add') {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Prepare and execute the SQL query to add the user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password_hash);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success'] = "User added successfully.";
        } 
        // If the action is 'delete', delete the user
        elseif ($action === 'delete') {
            $user_id = $_POST['user_id'];
            // Prepare and execute the SQL query to delete the user
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['success'] = "User deleted successfully.";
        }

        // Redirect back to the manage users page after action
        header("Location: manage_users.php");
        exit();
    }
}

// Fetch all users from the database
$sql = "SELECT * FROM users";
$users_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Users - Dreamscape Destinations</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    /* Style for the section title */
    .section-title {
        margin-bottom: 50px;
    }

    /* Style for table headers */
    .custom-table th {
        background-color: #4a4e69;
        color: white;
        text-align: center;
    }

    /* Center readonly input fields in the table */
    table.custom-table td input[readonly] {
        display: block;
        margin: 0 auto;
        text-align: center;
    }

    /* Style for input field placeholders */
    .form-control::placeholder {
        text-align: center;
    }

    /* Button styling */
    .btn-action {
        display: block;
        width: 100%;
        margin-bottom: 10px;
        text-align: center;
    }

    /* Style for secondary buttons */
    .btn-second {
        background-color: #6c757d;
        color: #f8f9fa;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
    }

    /* Hover effect for secondary buttons */
    .btn-second:hover {
        background-color: #9a8c98;
        color: black;
    }

    /* Active effect for secondary buttons */
    .btn-second:active {
        background-color: transparent;
        color: inherit;
    }

    /* Style for readonly input fields */
    input[readonly] {
        background-color: #f1f1f1;
    }

    /* Style for delete button in actions column */
    .action-btn {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Responsive styles for smaller screens */
    @media (max-width: 768px) {
        .navbar-brand {
            display: block;
            text-align: center;
        }

        .navbar-brand span {
            display: block;
        }

        /* Make all inputs full width on smaller screens */
        .custom-table input {
            width: 100%;
            box-sizing: border-box;
        }

        /* Make email inputs wrap text properly */
        .custom-table td input[type="email"] {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            width: 100% !important;
            min-width: 120px;
            box-sizing: border-box;
            padding: 6px;
        }

        /* Make all text and email inputs full width on smaller screens */
        .custom-table td input[type="text"],
        .custom-table td input[type="email"] {
            width: 100% !important;
            box-sizing: border-box;
        }

        /* Adjust email column width */
        .custom-table td input[type="email"] {
            min-width: 250px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Allow table to scroll horizontally on smaller screens */
        .table-responsive {
            overflow-x: auto;
        }

        /* Adjust user ID and username columns width */
        .custom-table td:first-child, .custom-table th:first-child {
            min-width: 150px;
            width: 100%;
            box-sizing: border-box;
        }

        .custom-table td:nth-child(2), .custom-table th:nth-child(2) {
            min-width: 150px;
            width: 100%;
            box-sizing: border-box;
        }

        /* Make all table cells take full width */
        .custom-table td {
            width: 100%;
        }

        /* Adjust last column for small screens */
        .custom-table td:last-child {
            min-width: 150px;
            white-space: nowrap;
        }
    }

    /* Extra responsiveness for very small screens */
    @media (max-width: 576px) {
        .modal-dialog {
            max-width: 95%;
            margin: 1.75rem auto;
        }

        .modal-content {
            padding: 10px;
        }
    }
  </style>
</head>

<body>
  <!-- Admin Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <!-- Brand Name and Admin Label -->
      <a class="qwigley-regular navbar-brand" href="admin.php">
        Dreamscape Destinations <span>- Admin</span>
      </a>
      <!-- Navbar Toggler for mobile view -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!-- Navbar Links -->
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <!-- Dashboard Link -->
          <li class="nav-item">
            <a class="nav-link" href="admin.php">
              <span class="material-symbols-outlined">home</span> Dashboard
            </a>
          </li>
          <!-- Manage Trips Link -->
          <li class="nav-item">
            <a class="nav-link" href="manage_trips.php">
              <span class="material-symbols-outlined">map</span> Manage Trips
            </a>
          </li>
          <!-- Manage Bookings Link -->
          <li class="nav-item">
            <a class="nav-link" href="manage_bookings.php">
              <span class="material-symbols-outlined">book</span> Manage Bookings
            </a>
          </li>
          <!-- Manage Users Link (Active) -->
          <li class="nav-item">
            <a class="nav-link active" href="manage_users.php">
              <span class="material-symbols-outlined">person</span> Manage Users
            </a>
          </li>
          <!-- Logout Button if admin is logged in -->
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

  <!-- Success Message (if any) -->
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" role="alert">
      <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <!-- Manage Users Section -->
  <div class="container my-5">
    <h2 class="section-title">Manage Users</h2>

    <!-- Add User Form -->
    <form id="addUserForm" method="post" class="mb-4 d-flex justify-content-center" onsubmit="return validateAddUserForm()">
      <div style="max-width: 900px; width: 100%; text-align: center;">
        <input type="hidden" name="action" value="add">
        <div class="row g-2 justify-content-center">
          <div class="col-md-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
          </div>
          <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
          </div>
          <div class="col-md-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
          </div>
        </div>
        <!-- Submit Button -->
        <div class="d-flex justify-content-center mt-3" style="width: 100%; margin: 0 auto;">
          <button type="submit" class="btn btn-theme btn-action" style="max-width: 200px; width: 100%;">Add</button>
        </div>
      </div>
    </form>

    <!-- Users Table -->
    <div class="table-responsive">
      <table class="table table-bordered custom-table">
        <thead class="table">
          <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through users and display them in rows -->
          <?php while ($user = $users_result->fetch_assoc()): ?>
            <tr>
              <form method="post" id="userForm<?php echo $user['user_id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                <!-- Display User Details -->
                <td><input type="text" name="user_id" class="form-control" value="<?php echo $user['user_id']; ?>" readonly></td>
                <td><input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly></td>
                <td><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly></td>
                <td class="action-btn">
                  <!-- Delete Button (Opens Modal) -->
                  <button type="button" class="btn btn-theme btn-action" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['user_id']; ?>">Delete</button>
                </td>
              </form>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Delete Confirmation Modals -->
  <?php 
  mysqli_data_seek($users_result, 0); // Reset pointer to loop again
  while ($user = $users_result->fetch_assoc()): ?>
    <div class="modal fade" id="deleteModal<?php echo $user['user_id']; ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header"><h5 class="card-title modal-title">Confirm Delete</h5></div>
          <div class="modal-body">Are you sure you want to delete this user?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
            <!-- Confirm Deletion -->
            <button type="button" class="btn btn-theme" onclick="submitForm(<?php echo $user['user_id']; ?>, 'delete')">Yes</button>
          </div>
        </div>
      </div>
    </div>
  <?php endwhile; ?>

  <!-- JavaScript to handle form submission after confirmation -->
  <script>
    // Validate Add User Form
    function validateAddUserForm() {
      const form = document.getElementById('addUserForm');
      const username = form.querySelector('input[name="username"]');
      const email = form.querySelector('input[name="email"]');
      const password = form.querySelector('input[name="password"]');
      
      // Check if username is empty
      if (!username.value.trim()) {
        alert("Username is required.");
        username.focus();
        return false;
      }

      // Check if email is in valid format
      const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
      if (!email.value.trim() || !emailPattern.test(email.value)) {
        alert("Please enter a valid email.");
        email.focus();
        return false;
      }

      // Check if password is at least 6 characters
      if (!password.value.trim() || password.value.length < 6) {
        alert("Password must be at least 6 characters long.");
        password.focus();
        return false;
      }

      return true; // If everything is valid, submit the form
    }

    // JavaScript to handle form submission after confirmation
    function submitForm(userId, action) {
      const form = document.getElementById('userForm' + userId);
      const hiddenAction = document.createElement('input');
      hiddenAction.type = 'hidden';
      hiddenAction.name = 'action';
      hiddenAction.value = action;
      form.appendChild(hiddenAction);
      form.submit();
    }
  </script>

  <!-- Footer -->
  <footer class="text-center py-3">
    <p class="mb-0">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
