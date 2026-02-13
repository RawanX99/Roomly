<?php
session_start();
include '../db.php';

if ($_SESSION['user_type'] != 1) {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id'])) {
  die("Hotel ID is required.");
}

$hotel_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = :hotel_id AND user_id = :user_id");
$stmt->execute(['hotel_id' => $hotel_id, 'user_id' => $_SESSION['user_id']]);
$hotel = $stmt->fetch();

if (!$hotel) {
  die("You are not authorized to view this hotel's bookings.");
}

$stmt = $pdo->prepare("
    SELECT 
        b.id AS booking_id,
        b.user_id,
        b.room_id,
        r.room_type,
        r.price,
        b.check_in_date,
        b.check_out_date,
        b.status,
        b.created_at,
        u.name as guest_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.hotel_id = :hotel_id
    ORDER BY b.created_at DESC
");
$stmt->execute(['hotel_id' => $hotel_id]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Bookings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style/style.css">
  <link rel="icon" href="../img/door.png">

</head>

<body>
  <?php include 'hotel_navbar.php'; ?>

  <div class="container mt-5">
    <h2>Manage Bookings for Hotel: <?php echo $hotel['hotel_name']; ?></h2>

    <?php if (count($bookings) > 0): ?>
      <table class="table table-bordered mt-4">
        <thead>
          <tr>
            <th>Booking ID</th>
            <th>Guest Name</th>
            <th>Room Type</th>
            <th>Price</th>
            <th>Check-in Date</th>
            <th>Check-out Date</th>
            <!--<th>Status</th>-->
            <th>Booked At</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $booking): ?>
            <tr>
              <td><?php echo $booking['booking_id']; ?></td>
              <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
              <td><?php echo $booking['room_type']; ?></td>
              <td><?php echo $booking['price']; ?> SAR</td>
              <td><?php echo $booking['check_in_date']; ?></td>
              <td><?php echo $booking['check_out_date']; ?></td>
              <!--<td><?php echo ucfirst($booking['status']); ?></td>-->
              <td><?php echo $booking['created_at']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No bookings available for this hotel.</p>
    <?php endif; ?>

    <a href="hotel_home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>