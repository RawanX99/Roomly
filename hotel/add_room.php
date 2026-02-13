<?php
session_start();
include '../db.php';

if ($_SESSION['user_type'] != 1) {
  header("Location: login.php");
  exit();
}
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$hotel = $stmt->fetch();

if (!$hotel) {
  echo "You don't have a hotel listed yet. Please <a href='add_hotel.php'>add a hotel</a>.";
  exit();
}

// form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $room_type = $_POST['room_type'];
  $price = $_POST['price'];
  $status = 'available';
  $stmt = $pdo->prepare("INSERT INTO rooms (hotel_id, room_type, price, status) VALUES (?, ?, ?, ?)");
  $stmt->execute([$hotel['id'], $room_type, $price, $status]);

header("Location: view_rooms.php?id=" . $hotel['id'] . "&added=yes");
exit();
//   echo "<div class='alert alert-success'>Room added successfully!</div>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Room</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style/style.css">
      <link rel="icon" href="../img/door.png">

</head>

<body>
  <?php include 'hotel_navbar.php'; ?>

  <div class="container mt-5">
      
      <!--<?php if ($flag): ?>-->
    <!--<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">-->
    <!--  <strong>Success!</strong> The room is added-->
    <!--        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
            
    <!--  </div>-->
      
       <!--<?php endif; ?>-->
       
    <form method="POST">
      <div class="mb-3">
        <label for="room_type" class="form-label">Room Type</label>
        <input type="text" name="room_type" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" name="price" class="form-control" step="0.1" required>
      </div>
      <button type="submit" class="btn btn-primary">Add Room</button>
    </form>
    <a href="hotel_home.php" class="btn btn-secondary mt-3">Back to hotel home </a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>