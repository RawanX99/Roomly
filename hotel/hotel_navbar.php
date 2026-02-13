<?php
include '../db.php';

// if (isset($_SESSION['hotel_id'])) {
//   $hotel_id = $_SESSION['hotel_id'];

//   $query = "SELECT hotel_name FROM hotels WHERE hotel_id = ?";
//   $stmt = $conn->prepare($query);
//   $stmt->bind_param("i", $hotel_id);
//   $stmt->execute();
//   $result = $stmt->get_result();

//   if ($row = $result->fetch_assoc()) {
//     $hotel_name = $row['hotel_name'];
//   } else {
//     $hotel_name = "Unknown Hotel";
//   }
//  } else {
 $hotel_name = "account option";
//  }
?>


<nav class="navbar navbar-expand-lg nav-background ">
  <div class="container">
    <a class="navbar-brand" href="hotel_home.php">Hotel Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="edit_hotel.php?id=<?php echo $hotel_id; ?>">Edit Hotel Details</a>
        </li>
        <!-- <li class="nav-item">
          <a class="nav-link" href="add_room.php?id=<?php echo $hotel_id; ?>">Add Room</a>
        </li> -->
        <li class="nav-item">
          <a class="nav-link" href="view_rooms.php?id=<?php echo $hotel_id; ?>">View Rooms</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="hotel_bookings.php?id=<?php echo $hotel_id; ?>">Manage Bookings</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="hotel_reviews.php?id=<?php echo $hotel_id; ?>">Reviews</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            <?php echo htmlspecialchars($hotel_name); ?>
          </a>

          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>