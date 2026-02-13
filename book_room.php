<?php
session_start();
include 'db.php';

if (!isset($_GET['room_id']) || !isset($_GET['hotel_id'])) {
  die("Room ID and Hotel ID are required.");
}

$room_id = $_GET['room_id'];
$hotel_id = $_GET['hotel_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = :room_id AND hotel_id = :hotel_id");
$stmt->execute(['room_id' => $room_id, 'hotel_id' => $hotel_id]);
$room = $stmt->fetch();

if (!$room) {
  die("Room not found in the specified hotel.");
}

$booking_success = false; // Flag to show the success message
$wrong_date = ""; // Optional: Initialize for use in display

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $check_in_date = $_POST['check_in_date'];
  $check_out_date = $_POST['check_out_date'];

  // Convert dates to timestamps for accurate comparison
  $check_in_ts = strtotime($check_in_date);
  $check_out_ts = strtotime($check_out_date);
  $booked_from_ts = strtotime($room['booked_from']);
  $booked_to_ts = strtotime($room['booked_to']);
  $now = time();

  // Check if booking overlaps with existing one
  if (
    $room['status'] == 'booked' &&
    !($check_out_ts <= $booked_from_ts || $check_in_ts >= $booked_to_ts)
  ) {
    $wrong_date = "The room is already booked for the selected dates.";
  }

  // Check for past date selection
  else if ($check_in_ts < $now || $check_out_ts < $now) {
    $wrong_date = "⚠️ Warning: You're trying to book a date in the past.";
  }
  else if ($check_in_ts >= $check_out_ts) {
    $wrong_date = "Check-out date must be after check-in date.";
}else {
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, hotel_id, check_in_date, check_out_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $room_id, $hotel_id, $check_in_date, $check_out_date]);

    $stmt_update = $pdo->prepare("UPDATE rooms SET booked_from = ?, booked_to = ?, status = 'booked' WHERE id = ?");
    $stmt_update->execute([$check_in_date, $check_out_date, $room_id]);

    $booking_success = true;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Room</title>
  <!--bs predefined class for styling-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!--to add beviour when click on the dropdown-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <link rel="icon" href="img/door.png">


  <style>
    .mt-70 {
      margin-top: 90px;
    }

    .form-label {
      font-weight: bold;
    }

    /*.btn-primary {*/
    /*  background-color: #007bff;*/
    /*  border-color: #007bff;*/
    /*}*/

    /*.btn-primary:hover {*/
    /*  background-color: #0056b3;*/
    /*  border-color: #0056b3;*/
    /*}*/

    .alert {
      display: none;
    }

    .alert.show {
      display: block;
    }
  </style>
</head>

<body>

  <?php include 'user_navbar.php'; ?>

  <div class="container mt-70">
      
        <!-- Success and failed messages popup -->
<?php if ($booking_success): ?>
  <div class="alert alert-success alert-dismissible fade show mt-3" role="alert" aria-live="assertive">
    <strong>Success!</strong> Your room booking was successful.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php elseif (!empty($wrong_date)): ?>
  <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert" aria-live="assertive">
    <strong>Error!</strong> <?= htmlspecialchars($wrong_date) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

    <h2 class="text-center mb-4">Book a <?php echo $room['room_type']; ?> </h2>
    
        <!--booking form-->
        
    <form method="POST">
      <div class="mb-3">
        <label for="check_in_date" class="form-label">Check-in Date</label>
        <input type="date" class="form-control" name="check_in_date" required>
      </div>
      <div class="mb-3">
        <label for="check_out_date" class="form-label">Check-out Date</label>
        <input type="date" class="form-control" name="check_out_date" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Book Now</button>
    </form>
  </div>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
