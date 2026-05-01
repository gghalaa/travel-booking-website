<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Meta and title for the page -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us - Dreamscape Destinations</title>

  <!-- External styles and fonts -->
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />

  <style>
    /* Main container for the page */
    .about-container {
      max-width: 1000px;
      margin: auto;
      padding: 30px 20px;
    }

    /* Styling for sections */
    .about-section {
      margin-bottom: 40px;
    }

    /* Heading for each section */
    .about-section h2 {
      color: #333;
      border-bottom: 2px solid #ccc;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }

    /* Gallery of images */
    .gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: space-between;
      margin-top: 20px;
    }

    /* Style for each image in the gallery */
    .gallery img {
      flex: 1 1 calc(33.333% - 10px);
      max-width: 100%;
      height: 200px;
      border-radius: 8px;
      object-fit: cover;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    /* Hover effect for gallery images */
    .gallery img:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    /* FAQ item styling */
    .faq-item {
      margin-bottom: 20px;
    }

    /* Margin for FAQ text */
    .faq-item p {
      margin-top: 5px;
    }

    /* Caption styling for gallery images */
    .caption {
      text-align: left;
      margin: 0;
    }

    /* Styling for plain links */
    .plain-link {
      color: inherit;
      text-decoration: none;
    }

    /* Accordion button style */
    .accordion-button {
      background-color: #4a4e69;
      color: white;
      font-weight: 600;
      border: none;
      box-shadow: none;
      transition: background-color 0.3s ease, color 0.3s ease;
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      text-align: left;
    }

    /* Style for accordion button when not collapsed */
    .accordion-button:not(.collapsed) {
      background-color: #9a8c98;
      color: black;
    }

    /* Styling when accordion button is focused */
    .accordion-button:focus {
      box-shadow: none;
    }

    /* Styling for accordion body */
    .accordion-body {
      background-color: #f8f8f8;
      color: #333;
      border-top: 1px solid #ddd;
    }

    /* White arrow when the accordion is collapsed */
    .accordion-button::after {
      filter: brightness(0) invert(1);
    }

    /* Black arrow when the accordion is expanded */
    .accordion-button:not(.collapsed)::after {
      filter: brightness(0);
    }
  </style>
</head>

<body>

  <!-- Navbar Section: Navigation bar with links to different pages -->
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
            <a class="nav-link active" href="aboutus.php">
              <span class="material-symbols-outlined">info</span> About Us
            </a>
          </li>
          
          <!-- Check if user is logged in -->
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

  <!-- About Us Section: Information about the company -->
  <div class="about-container">
    <h2 class="section-title text-center mb-4">About Us</h2>

    <!-- Our Story: Brief introduction of the company -->
    <section class="about-section">
      <h2 class="card-title">Our Story</h2>
      <p>Dreamscape Destinations has been a trusted name in the travel industry for over 25 years. Since our inception, we've dedicated ourselves to offering exceptional travel experiences that create lasting memories for our customers around the globe.</p>
    </section>

    <!-- Our Values: Company values and mission -->
    <section class="about-section">
      <h2 class="card-title">Our Values</h2>
      <p>We are officially approved by leading travel and tourism organizations. Our core policy revolves around care, quality, and customer satisfaction. Whether you're looking for relaxation, adventure, or cultural exploration, we make sure every step of your journey is smooth and enjoyable.</p>
    </section>

    <!-- Our Network: Information about the company's branches -->
    <section class="about-section">
      <h2 class="card-title">Our Network</h2>
      <p>With branches in numerous cities and a dedicated team of professional staff, we are always close to you and ready to assist. Our team works diligently to plan and manage unforgettable 7-day trips for travelers from all walks of life.</p>
    </section>

    <!-- Tour Memories: Image gallery of past tours -->
    <section class="about-section">
      <h2 class="card-title">Tour Memories</h2>
      <div class="gallery">
        <img src="assets/images/tourgroup1.jpg" alt="Tour Group 1">
        <img src="assets/images/tourgroup2.jpg" alt="Tour Group 2">
        <img src="assets/images/tourgroup3.jpg" alt="Tour Groups 3">
      </div>
    </section>

    <!-- Frequently Asked Questions: Accordion with common inquiries -->
    <section class="about-section">
      <h2 class="card-title">Frequently Asked Questions</h2>
      <div class="accordion" id="faqAccordion">

        <!-- FAQ 1: How long are your trips? -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
              aria-expanded="true" aria-controls="collapseOne">
              How long are your trips?
            </button>
          </h2>
          <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
            data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              All of our tours are thoughtfully designed 7-day experiences, giving you the perfect balance of adventure, relaxation, and cultural exploration in every destination.
            </div>
          </div>
        </div>

        <!-- FAQ 2: Can I customize the itinerary? -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
              Can I customize the itinerary?
            </button>
          </h2>
          <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
            data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Each trip follows a carefully curated 7-day itinerary crafted by our travel experts to maximize your experience. You’re welcome to choose any 7-day period that works best for you, our team will be there to guide you and ensure your comfort throughout the journey.
            </div>
          </div>
        </div>

        <!-- FAQ 3: Are your tours family-friendly? -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
              Are your tours family-friendly?
            </button>
          </h2>
          <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
            data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              Absolutely! Our tours are ideal for families, couples, solo travelers, and groups. We include a variety of activities suitable for different age groups and interests.
            </div>
          </div>
        </div>

        <!-- FAQ 4: Where are your branches located? -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingFour">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
              Where are your branches located?
            </button>
          </h2>
          <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
            data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              We have offices in major cities including New York, London, Dubai, Sydney, and many more. Our friendly local staff are always happy to assist you. Follow us on social media to find a branch near you.
            </div>
          </div>
        </div>

        <!-- FAQ 5: How can I contact your support team? -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="headingFive">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
              How can I contact your support team?
            </button>
          </h2>
          <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
            data-bs-parent="#faqAccordion">
            <div class="accordion-body">
              You can reach our support team by emailing <strong><a href="mailto:support@dreamscapedestinations.com" class="plain-link">support@dreamscapedestinations.com</a></strong> or by visiting any of our local branches.
            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- Social Media Section: Information on following the company on social media -->
    <section class="about-section">
      <h2 class="card-title">Follow Us on Social Media</h2>
      <p>Stay updated on new trips, travel tips, and unforgettable moments from our travelers around the world.</p>
      <ul class="social-links">
        <li>Facebook: @dreamscapedestinations</li>
        <li>Instagram: @dreamscapedestinations</li>
        <li>Twitter: @dreamscapedest</li>
        <li>YouTube: Dreamscape Destinations</li>
      </ul>
    </section>
  </div>

  <!-- Footer Section: Copyright notice -->
  <footer>
    <p class="mb-0 text-center">Copyright &copy; 2025 Dreamscape Destinations. All rights reserved.</p>
  </footer>

  <!-- Bootstrap JS: Bootstrap JavaScript bundle for responsive features -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
