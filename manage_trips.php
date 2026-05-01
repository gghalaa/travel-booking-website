<?php
// Start the session for user login tracking
session_start();

// Initialize DB connection
include 'db_config.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check what action is requested (add, edit, delete)
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Add a new trip to the database
        if ($action === 'add') {
            $title = $_POST['title'];
            $location = $_POST['location'];
            $summary = $_POST['summary'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $itinerary = $_POST['itinerary'];

            $stmt = $conn->prepare("INSERT INTO trips (title, location, summary, price, description, itinerary) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdss", $title, $location, $summary, $price, $description, $itinerary);
            $stmt->execute();
            $stmt->close();

            // Set a success message in the session
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Trip added successfully!'];
        }
        // Edit an existing trip in the database
        elseif ($action === 'edit') {
            $trip_id = $_POST['trip_id'];
            $title = $_POST['title'];
            $location = $_POST['location'];
            $summary = $_POST['summary'];
            $price = $_POST['price'];
            $description = $_POST['description'];
            $itinerary = $_POST['itinerary'];

            $stmt = $conn->prepare("UPDATE trips SET title=?, location=?, summary=?, price=?, description=?, itinerary=? WHERE trip_id=?");
            $stmt->bind_param("sssdssi", $title, $location, $summary, $price, $description, $itinerary, $trip_id);
            $stmt->execute();
            $stmt->close();

            // Set a success message in the session
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Trip updated successfully!'];
        }
        // Delete a trip from the database
        elseif ($action === 'delete') {
            $trip_id = $_POST['trip_id'];
            $stmt = $conn->prepare("DELETE FROM trips WHERE trip_id=?");
            $stmt->bind_param("i", $trip_id);
            $stmt->execute();
            $stmt->close();

            // Set a success message in the session
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Trip deleted successfully!'];
        }

        // Redirect back to manage trips page after action
        header("Location: manage_trips.php");
        exit();
    }
}

// Handle search functionality for trips
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM trips WHERE title LIKE ? OR location LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$trips_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Trips - Dreamscape Destinations</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <style>
    /* Style for the section titles */
    .section-title {
      margin-bottom: 50px;
    }

    /* Style for the custom table */
    .custom-table td {
      text-align: center;
      padding: 12px;
    }

    .custom-table th {
      background-color: #4a4e69;
      color: white;
      text-align: center;
      padding: 12px;
    }

    /* Style for action buttons */
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

    /* Centering the form fields */
    input.form-control,
    textarea.form-control {
      text-align: center;
    }

    /* Allow textarea to resize vertically */
    textarea.form-control {
      vertical-align: middle;
      resize: vertical;
    }

    /* Align table data vertically */
    td {
      vertical-align: middle;
    }

    /* Display buttons within table data */
    td .btn {
      display: block;
      margin: 5px auto;
    }

    /* Responsive styling for small screens */
    @media (max-width: 768px) {
      /* Stack elements vertically on small screens */
      .d-flex {
        flex-direction: column;
        align-items: center;
      }

      /* Ensure form fields take up full width on small screens */
      .d-flex .d-flex-column {
        min-width: 100%;
      }

      /* Make the table scrollable on small screens */
      .custom-table {
        overflow-x: auto;
        display: block;
        white-space: nowrap;
      }

      /* Adjust table padding for small screens */
      .table th,
      .table td {
        white-space: nowrap;
        padding: 12px;
      }

      /* Style adjustments for custom table */
      .custom-table th {
        margin-bottom: 5px;
      }

      .custom-table td:first-child,
      .custom-table td:nth-child(2) {
        width: 30%;
      }

      .custom-table td:nth-child(3) {
        width: 20%;
      }

      .custom-table td:nth-child(4),
      .custom-table td:nth-child(5) {
        width: 15%;
      }

      .custom-table td {
        min-width: 150px;
        word-wrap: break-word;
      }

      /* Adjust navbar for smaller screens */
      .navbar-brand {
        display: block;
        text-align: center;
      }

      .navbar-brand span {
        display: block;
      }

      /* Flexbox adjustments for small screens */
      .d-flex {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
      }
    }

    /* Add style for expandable table rows */
    .custom-table td {
      position: relative;
      overflow: hidden;
      padding: 15px;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .custom-table td.expand {
      white-space: normal;
      word-wrap: break-word;
      height: auto;
      background-color: #f0f0f0;
      transition: all 0.3s ease-in-out;
    }

    /* Style for auto-expanding textarea */
    textarea.auto-expand {
      min-height: 130px;
      overflow-y: hidden;
    }
  </style>
</head>

<body>
  <!-- Admin Navigation Bar -->
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
          <!-- Navigation links -->
          <li class="nav-item">
            <a class="nav-link" href="admin.php">
              <span class="material-symbols-outlined">home</span> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="manage_trips.php">
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
          <!-- Logout option for logged-in admin -->
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

  <!-- Display any session alerts -->
  <?php
  if (isset($_SESSION['alert'])) {
      $type = $_SESSION['alert']['type'];
      $message = $_SESSION['alert']['message'];
      echo "<div class='alert alert-$type fade show' role='alert'>
              $message
            </div>";
      unset($_SESSION['alert']);
  }
  ?>

  <div class="container my-5">
    <!-- Manage Trips Section Title -->
    <h2 class="section-title">Manage Trips</h2>
    
    <!-- Search form for trips -->
    <form method="get" class="d-flex justify-content-center mb-4" style="max-width: 600px; margin: 0 auto;">
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

    <!-- Add Trip Form -->
    <div class="text-center mb-4">
      <form id="addTripForm" method="post" class="d-inline-block w-100" onsubmit="validateAndSubmitForm(); return false;" style="max-width: 1000px;">
        <input type="hidden" name="action" value="add">
        
        <!-- Trip Title and Location Fields -->
        <div class="d-flex justify-content-center gap-2">
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <div><input type="text" name="title" class="form-control" placeholder="Trip Title" required></div>
          </div>
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <div><input type="text" name="location" class="form-control" placeholder="Location" required></div>
          </div>
        </div>

        <!-- Trip Summary and Price Fields -->
        <div class="d-flex justify-content-center gap-2 mt-3">
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <div><input type="text" name="summary" class="form-control" placeholder="Summary" required></div>
          </div>
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <div><input type="number" name="price" class="form-control" placeholder="Price" step="0.01" required min="0"></div>
          </div>
        </div>

        <!-- Trip Description and Itinerary Fields -->
        <div class="d-flex justify-content-center gap-2 mt-3">
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <textarea name="description" class="form-control" placeholder="Description" required style="height: 100%; min-height: 130px;"></textarea>
          </div>
          <div class="d-flex flex-column gap-2" style="min-width: 45%;">
            <textarea name="itinerary" class="form-control" placeholder="Itinerary" required style="height: 100%; min-height: 130px;"></textarea>
          </div>
        </div>

        <!-- Add Button -->
        <div class="d-flex justify-content-center mt-3" style="width: 100%; margin: 0 auto;">
          <button type="submit" class="btn btn-theme btn-action" style="max-width: 200px; width: 100%;">Add</button>
        </div>

      </form>
    </div>

    <!-- Trips Table -->
    <table class="table table-bordered custom-table">
      <thead class="table">
        <tr>
          <th>Title</th>
          <th>Location</th>
          <th>Summary</th>
          <th>Price</th>
          <th>Description</th>
          <th>Itinerary</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <!-- Loop through each trip and display it -->
        <?php while ($trip = $trips_result->fetch_assoc()): ?>
          <tr>
            <form method="post" id="tripForm<?php echo $trip['trip_id']; ?>">
              <input type="hidden" name="trip_id" value="<?php echo $trip['trip_id']; ?>">
              <!-- Trip Title -->
              <td><textarea name="title" class="form-control auto-expand" required><?php echo htmlspecialchars($trip['title']); ?></textarea></td>
              <!-- Trip Location -->
              <td><textarea name="location" class="form-control auto-expand" required><?php echo htmlspecialchars($trip['location']); ?></textarea></td>
              <!-- Trip Summary -->
              <td><textarea name="summary" class="form-control auto-expand" required><?php echo htmlspecialchars($trip['summary']); ?></textarea></td>
              <!-- Trip Price -->
              <td><input type="number" name="price" class="form-control" value="<?php echo htmlspecialchars($trip['price']); ?>" step="0.01" required></td>
              <!-- Trip Description -->
              <td><textarea name="description" class="form-control auto-expand" required><?php echo htmlspecialchars($trip['description']); ?></textarea></td>
              <!-- Trip Itinerary -->
              <td><textarea name="itinerary" class="form-control auto-expand" required><?php echo htmlspecialchars($trip['itinerary']); ?></textarea></td>
              <!-- Edit and Delete Buttons -->
              <td class="text-center">
                <button type="button" class="btn btn-theme btn-action" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $trip['trip_id']; ?>">Edit</button>
                <button type="button" class="btn btn-second btn-action" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $trip['trip_id']; ?>">Delete</button>
              </td>
            </form>
          </tr>

          <!-- Edit Modal -->
          <div class="modal fade" id="editModal<?php echo $trip['trip_id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="card-title modal-title">Confirm Update</h5>
                </div>
                <div class="modal-body">
                  Are you sure you want to update this trip?
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
                  <button type="button" class="btn btn-theme" onclick="submitForm(<?php echo $trip['trip_id']; ?>, 'edit')">Yes</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Delete Modal -->
          <div class="modal fade" id="deleteModal<?php echo $trip['trip_id']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header"><h5 class="card-title modal-title">Confirm Delete</h5></div>
                <div class="modal-body">Are you sure you want to delete this trip?</div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-second" data-bs-dismiss="modal">No</button>
                  <button type="button" class="btn btn-theme" onclick="submitForm(<?php echo $trip['trip_id']; ?>, 'delete')">Yes</button>
                </div>
              </div>
            </div>
          </div>

        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script>
  // Form validation before submission
  function validateAndSubmitForm() {
    const form = document.getElementById('addTripForm');
    
    // Validate Title
    const title = form.querySelector('input[name="title"]');
    if (!title.value.trim()) {
      alert('Title is required');
      title.focus();
      return;
    }

    // Validate Location
    const location = form.querySelector('input[name="location"]');
    if (!location.value.trim()) {
      alert('Location is required');
      location.focus();
      return;
    }

    // Validate Price (should be a positive number greater than 0)
    const price = form.querySelector('input[name="price"]');
    if (!price.value.trim() || parseFloat(price.value) <= 0) {
      alert('Price must be a valid number greater than 0');
      price.focus();
      return;
    }


    // Validate Description
    const description = form.querySelector('textarea[name="description"]');
    if (!description.value.trim()) {
      alert('Description is required');
      description.focus();
      return;
    }

    // Validate Itinerary
    const itinerary = form.querySelector('textarea[name="itinerary"]');
    if (!itinerary.value.trim()) {
      alert('Itinerary is required');
      itinerary.focus();
      return;
    }

    // If all validations pass, submit the form
    form.submit();
  }
  </script>

  <script>
  // Submit the form for editing a trip based on tripId
  function submitEditForm(tripId) {
    document.getElementById('editForm' + tripId).submit();
    }
  </script>

  <script>
    // Submit the form with an action (e.g. cancel) for a trip
    function submitForm(tripId, action) {
      const form = document.getElementById('tripForm' + tripId);
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

  <script>
    // Adjust textarea height on page load and when input occurs
    document.addEventListener('DOMContentLoaded', function () {
      const textareas = document.querySelectorAll('.auto-expand');
      textareas.forEach(textarea => {
        textarea.addEventListener('input', function () {
          this.style.height = 'auto';  // Reset height
          this.style.height = (this.scrollHeight) + 'px';  // Adjust height
        });
       textarea.dispatchEvent(new Event('input'));  // Trigger input event on load
      });
    });
  </script>

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

  <footer class="text-center py-3">
    <p class="mb-0">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>