
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: #1d364a">
  <div class="container">
      
      
    <a class="navbar-brand" style="font-size:25px; font-weight:bold;letter-spacing:3px; font-family:'Sofia';" href="user_dashboard.php"><span style="color:#6689ff; font-size:25px">R</span>oomly</a>
    
    <!--button display-->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <!--display-->
<div class="collapse navbar-collapse" id="navbarNav">

      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="user_dashboard.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link " href="view_booking.php">Bookings</a>
        </li>

      </ul>
      
      
      <?php if (isset($_SESSION['user_id'])) { ?>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                account option
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      <?php } else { ?>
        <a href="login.php" class="btn btn-success">Logout</a>
      <?php } ?>
      
      
    </div>
  </div>
</nav>
























<!-- إضافة Bootstrap JS لتفعيل الدروب داون -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href='https://fonts.googleapis.com/css?family=Sofia' rel='stylesheet'>

