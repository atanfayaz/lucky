<?php
include 'db.php'; // Database connection
session_start();

// CSRF Token Generation
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a random CSRF token
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF Token on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed.");
    }

    // Handle form submission securely
    $category = htmlspecialchars($_POST['category']);
    $name = htmlspecialchars($_POST['name']);
    $contact = htmlspecialchars($_POST['contact']);
    $donation = htmlspecialchars($_POST['donation']);
    $upi_id = htmlspecialchars($_POST['upi_id']);

    // Validate user inputs
    if (empty($name) || empty($contact) || empty($upi_id)) {
        $error = "All fields are required!";
    } else {
        // Prepared statement to prevent SQL Injection
        $stmt = $conn->prepare("INSERT INTO entries (category, name, contact, donation, upi_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $category, $name, $contact, $donation, $upi_id);
        if ($stmt->execute()) {
            $success = "Entry submitted successfully! Please complete the UPI payment.";
        } else {
            $error = "Error in submitting entry. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lucky Draw Contest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For Icons -->
    <!-- Font Awesome (for the moon/sun icons) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<style>
  /* General Styles */
  body {
    font-family: 'Inter', sans-serif;
    background: #f8f9fc; /* Light background for contrast */
    color: #2c3e50; /* Dark text for readability */
    margin: 0;
    padding: 0;
    line-height: 1.6;
  }

  .navbar {
  background: linear-gradient(90deg, rgba(255, 136, 34, 1) 0%, rgba(255, 72, 0, 1) 100%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 15px 30px;
}

.navbar-nav .nav-link {
  color: white !important;
  transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
  color: #FFD700 !important;
}

.navbar-toggler {
  border-color: #FFD700;
}

.navbar-toggler-icon {
  background-color: #FFD700;
}
.navbar-nav .nav-link {
  position: relative;
  padding: 10px 15px;
}

.navbar-nav .nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #FFD700;
  transform: scaleX(0);
  transform-origin: bottom right;
  transition: transform 0.3s ease-out;
}

.navbar-nav .nav-link:hover::after {
  transform: scaleX(1);
  transform-origin: bottom left;
}
#toggleDarkMode {
  background-color: #FFD700;
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

#toggleDarkMode:hover {
  background-color: #FF5722;
  transform: scale(1.1);
}

#toggleDarkMode i {
  font-size: 20px;
  color: #fff;
}
.navbar-toggler {
  border: 1px solid #FFD700;
}

.navbar-toggler-icon {
  background-color: #FFD700;
}

@media (max-width: 767px) {
  .navbar {
    background: rgba(0, 0, 0, 0.7);
  }
}

  
  /* Cards */
  .card {
    border-radius: 18px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
  }

  .card-body {
    padding: 1.5rem;
  }

  .card-title {
    font-size: 1.4rem;
    font-weight: bold;
    color: #6a11cb;
  }

  .card-text {
    font-size: 1rem;
    color: #2c3e50;
  }
 /* Carousel Styles */
 .carousel-inner {
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
  }

  .carousel-item img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    filter: brightness(85%);
    transition: transform 0.5s ease, filter 0.5s ease;
  }

  .carousel-item img:hover {
    filter: brightness(100%);
    transform: scale(1.05);
  }

  .carousel-caption {
    position: absolute;
    bottom: 20%;
    left: 10%;
    right: 10%;
    background: rgba(0, 0, 0, 0.6);
    color: #fff;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    animation: fadeInUp 1s ease-in-out;
  }

  .carousel-caption h5 {
    font-size: 2rem;
    font-weight: bold;
    color: #ffd700;
  }

  .carousel-caption p {
    font-size: 1.2rem;
  }

  .carousel-control-prev,
  .carousel-control-next {
    width: 5%;
  }

  .carousel-control-prev-icon,
  .carousel-control-next-icon {
    filter: invert(100%);
  }

  .carousel-indicators [data-bs-target] {
    background-color: #ffd700;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    transition: transform 0.3s ease;
  }

  .carousel-indicators [data-bs-target].active {
    transform: scale(1.2);
    background-color: #ff7e5f;
  }

  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  /* Buttons */
  .btn-primary {
    background: linear-gradient(90deg, #ff7e5f, #feb47b); /* Soft gradient */
    border: none;
    border-radius: 25px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    color: #ffffff;
    transition: background 0.3s ease, transform 0.3s ease;
  }

  .btn-primary:hover {
    background: linear-gradient(90deg, #ff6f61, #fe946b);
    transform: translateY(-3px);
  }

  .btn-success {
    background: linear-gradient(90deg, #42e695, #3bb2b8);
    border: none;
    border-radius: 25px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    color: #ffffff;
    transition: background 0.3s ease, transform 0.3s ease;
  }

  .btn-success:hover {
    background: linear-gradient(90deg, #34d178, #34a89a);
    transform: translateY(-3px);
  }

  /* Feedback Form */
  .feedback-form {
    background-color: #ffffff;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
  }

  .feedback-form:hover {
    transform: translateY(-5px);
  }

  /* Footer */
  footer {
    background: linear-gradient(90deg, #2575fc, #6a11cb); /* Footer gradient */
    color: white;
    padding: 2rem 0;
  }

  footer h5 {
    font-weight: bold;
    font-size: 1.2rem;
  }

  footer a {
    color: #ffd700;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  footer a:hover {
    color: #fff;
  }

  footer .social-icons a {
    color: #ffd700;
    margin-right: 10px;
    font-size: 1.5rem;
    transition: color 0.3s ease, transform 0.3s ease;
  }

  footer .social-icons a:hover {
    color: #ffffff;
    transform: scale(1.1);
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .card {
      margin-bottom: 20px;
    }

    .navbar-nav .nav-link {
      font-size: 0.9rem;
    }
  }

  @media (max-width: 576px) {
    .feedback-form {
      padding: 20px;
    }

    .btn-primary,
    .btn-success {
      padding: 0.5rem 1rem;
      font-size: 0.9rem;
    }
  }

  /* Animations */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  body {
    animation: fadeIn 0.5s ease-in-out;
  }
</style>

</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="your-logo.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top">
      Lucky Draw
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="live-winner.php">Live Winner</a></li>
        <li class="nav-item"><a class="nav-link active" href="winner_results.php">Winner List</a></li>
        <li class="nav-item"><a class="nav-link active" href="feedback.php">Feedback</a></li>
        <li class="nav-item">
          <button class="btn btn-success" id="toggleDarkMode"><i class="fas fa-moon"></i></button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Carousel -->
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="1.jpeg" class="d-block w-100 img-fluid" alt="First Slide">
      <div class="carousel-caption d-none d-md-block">
        <h5>Winner Of Normal</h5>
        <p>............Winners of Normal Are As ...........</p>
        <a href="winner_results.php" class="btn btn-primary">Enter</a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="2.jpeg" class="d-block w-100 img-fluid" alt="Second Slide">
      <div class="carousel-caption d-none d-md-block">
        <h5>Winner Of Pro</h5>
        <p>............Winners Of Pro Are As .........................</p>
        <a href="winner_results.php" class="btn btn-secondary">Enter</a>
      </div>
    </div>
    <div class="carousel-item">
      <img src="3.png" class="d-block w-100 img-fluid" alt="Third Slide">
      <div class="carousel-caption d-none d-md-block">
        <h5>Winner Of Mega</h5>
        <p>............Winners Of Mega  Are As ..........................</p>
        <a href="winner_results.php" class="btn btn-success">Enter</a>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<!-- Card Options (Normal, Pro, Mega) -->
<div class="container my-5">
  <div class="row">
    <!-- Normal Card -->
    <div class="col-md-4">
      <div class="card">
        <img src="https://img.freepik.com/free-vector/enter-lucky-draw-win-prizes-yellow-background-design_1017-51273.jpg" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title">Normal</h5>
          <p class="card-text">Entry Fee:RS 10</p>
          <p class ="card-text">Winner Will Get RS 2000</p>
          <form action="entry_form_normal.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
            <label for="donationSelectNormal" class="form-label">Choose Donation:</label>
            <select class="form-select" name="donation" id="donationSelectNormal">
              <option value="NGO">NGOs</option>
              <option value="Medical">Medical</option>
              <option value="Other">Other</option>
            </select>
            <input type="hidden" name="category" value="Normal">
            <button class="btn btn-primary mt-3" type="submit">Enter</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Pro Card -->
    <div class="col-md-4">
      <div class="card">
        <img src="https://img.freepik.com/free-vector/enter-lucky-draw-win-prizes-yellow-background-design_1017-51273.jpg" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title">Pro</h5>
          <p class="card-text">Entry Fee: Rs 25</p>
          <p class ="card-text">Winner Will Get RS 4000</p>
          <form action="pro_form.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
            <label for="donationSelectPro" class="form-label">Choose Donation:</label>
            <select class="form-select" name="donation" id="donationSelectPro">
              <option value="NGO">NGOs</option>
              <option value="Medical">Medical</option>
              <option value="Other">Other</option>
            </select>
            <input type="hidden" name="category" value="Pro">
            <button class="btn btn-primary mt-3" type="submit">Enter</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Mega Card -->
    <div class="col-md-4">
      <div class="card">
        <img src="https://img.freepik.com/free-vector/enter-lucky-draw-win-prizes-yellow-background-design_1017-51273.jpg" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title">Mega</h5>
          <p class="card-text">Entry Fee: Rs 50</p>
          <p class ="card-text">Winner Will Get RS 10000</p>
          <form action="entry_form_mega.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
            <label for="donationSelectMega" class="form-label">Choose Donation:</label>
            <select class="form-select" name="donation" id="donationSelectMega">
              <option value="NGO">NGOs</option>
              <option value="Medical">Medical</option>
              <option value="Other">Other</option>
            </select>
            <input type="hidden" name="category" value="Mega">
            <button class="btn btn-primary mt-3" type="submit">Enter</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Winner Results -->
<div class="container my-5">
  <h3>Winner Results</h3>
  <a href="winner_results.php"> <button class="btn btn-primary mt-3" type="submit">Enter</button></a>
</div>

<!-- Feedback Form -->
<div class="container my-5 feedback-form">
  <h3>Feedback</h3>
  <form action="submit_feedback.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
    <div class="mb-3">
      <label for="name" class="form-label">Your Name</label>
      <input type="text" class="form-control" name="name" required>
    </div>
    <div class="mb-3">
      <label for="feedback" class="form-label">Your Feedback</label>
      <textarea class="form-control" name="feedback" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-success">Submit Feedback</button>
  </form>
</div>
<!-- Footer -->
<footer class="py-5" style="background: linear-gradient(to bottom, #2a2a72, #009ffd); color: white;">
  <div class="container">
    <div class="row">
      <!-- About Us Section -->
      <div class="col-lg-4 col-md-6 mb-4">
        <h5 class="fw-bold">About Us</h5>
        <p class="small">
          At Lucky Draw Contest, we bring excitement to your life with thrilling contests and the opportunity to make a difference. Participate, donate, and win big while supporting meaningful causes!
        </p>
      </div>

      <!-- Quick Links Section -->
      <div class="col-lg-4 col-md-6 mb-4">
        <h5 class="fw-bold">Quick Links</h5>
        <ul class="list-unstyled">
          <li>
            <a href="index.php" class="text-white text-decoration-none d-flex align-items-center mb-2 hover-link">
              <i class="bi bi-house-door me-2"></i> Home
            </a>
          </li>
          <li>
            <a href="about.php" class="text-white text-decoration-none d-flex align-items-center mb-2 hover-link">
              <i class="bi bi-info-circle me-2"></i> About
            </a>
          </li>
          <li>
            <a href="contact.php" class="text-white text-decoration-none d-flex align-items-center mb-2 hover-link">
              <i class="bi bi-telephone me-2"></i> Contact Us
            </a>
          </li>
          <li>
            <a href="terms.php" class="text-white text-decoration-none d-flex align-items-center mb-2 hover-link">
              <i class="bi bi-file-earmark-text me-2"></i> Terms & Conditions
            </a>
          </li>
          <li>
            <a href="privacy.php" class="text-white text-decoration-none d-flex align-items-center hover-link">
              <i class="bi bi-shield-lock me-2"></i> Privacy Policy
            </a>
          </li>
        </ul>
      </div>

      <!-- Social Media Section -->
      <div class="col-lg-4 col-md-12 mb-4">
        <h5 class="fw-bold">Stay Connected</h5>
        <div class="d-flex justify-content-start gap-3 mt-3">
          <a href="https://instagram.com" target="_blank" class="social-media-card">
            <img src="https://via.placeholder.com/100x100?text=Insta" alt="Instagram" class="rounded-circle shadow">
            <span class="social-label">Instagram</span>
          </a>
          <a href="https://facebook.com" target="_blank" class="social-media-card">
            <img src="https://via.placeholder.com/100x100?text=FB" alt="Facebook" class="rounded-circle shadow">
            <span class="social-label">Facebook</span>
          </a>
          <a href="https://youtube.com" target="_blank" class="social-media-card">
            <img src="https://via.placeholder.com/100x100?text=YT" alt="YouTube" class="rounded-circle shadow">
            <span class="social-label">YouTube</span>
          </a>
        </div>
      </div>
    </div>

    <hr class="bg-light mt-5">

    <!-- Footer Bottom -->
    <div class="row text-center">
      <div class="col">
        <p class="small mb-1">&copy; 2025 Lucky Draw Contest. All Rights Reserved.</p>
        <p class="small">
          Designed by 
          <a href="https://yourwebsite.com" target="_blank" class="text-white fw-bold text-decoration-none hover-link">Fayaz Ali</a>.
        </p>
      </div>
    </div>
  </div>

  <style>
    /* Hover Effects */
    .hover-link:hover {
      color: #ffdd59 !important;
      text-decoration: underline;
    }

    /* Social Media Card Styles */
    .social-media-card {
      text-align: center;
      text-decoration: none;
      color: white;
      transition: all 0.3s ease;
    }

    .social-media-card:hover img {
      transform: scale(1.1);
      filter: brightness(0.9);
    }

    .social-media-card:hover .social-label {
      color: #ffdd59;
    }

    .social-media-card img {
      width: 60px;
      height: 60px;
      transition: all 0.3s ease;
    }

    .social-label {
      display: block;
      margin-top: 5px;
      font-size: 0.9rem;
      font-weight: bold;
    }

    /* Footer Background Style */
    footer {
      font-family: "Poppins", sans-serif;
      font-size: 0.9rem;
      line-height: 1.5;
    }

    /* Media Queries for Mobile */
    @media (max-width: 768px) {
      .social-media-card img {
        width: 50px;
        height: 50px;
      }

      .social-label {
        font-size: 0.8rem;
      }
    }



    /* Dark Mode Styles */
body.dark-mode {
  background-color: #121212;
  color: white;
}

body.dark-mode .navbar {
  background-color: #333;
}

body.dark-mode .navbar-nav .nav-link {
  color: white;
}

body.dark-mode .navbar-nav .nav-link:hover {
  color: #FFD700;
}

  </style>
</footer>




<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
<script>
  // Dark Mode Toggle Script
  document.getElementById('toggleDarkMode').addEventListener('click', function () {
    document.body.classList.toggle('dark-mode');
    const mode = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
    localStorage.setItem('theme', mode);

    // Change icon based on mode
    const icon = document.getElementById('toggleDarkMode').querySelector('i');
    if (mode === 'dark') {
      icon.classList.remove('fa-moon');
      icon.classList.add('fa-sun');
    } else {
      icon.classList.remove('fa-sun');
      icon.classList.add('fa-moon');
    }
  });

  // Retain Theme on page reload
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    // Change the icon to sun when dark mode is active
    const icon = document.getElementById('toggleDarkMode').querySelector('i');
    icon.classList.remove('fa-moon');
    icon.classList.add('fa-sun');
  }
</script>

</body>
</html>
