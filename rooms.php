<?php
session_start();
include 'db.php';

if (!isset($_GET['hotel_id'])) {
  die("Hotel ID is required.");
}
$hotel_id = $_GET['hotel_id'];
// Retrieving rooms
$stmt_rooms = $pdo->prepare("SELECT * FROM rooms WHERE hotel_id = :hotel_id");
$stmt_rooms->execute(['hotel_id' => $hotel_id]);
$rooms = $stmt_rooms->fetchAll();
// retrieve hotel
$stmt_hotel = $pdo->prepare("SELECT * FROM hotels WHERE id = :hotel_id");
$stmt_hotel->execute(['hotel_id' => $hotel_id]);
$hotel = $stmt_hotel->fetch();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rooms in <?php echo $hotel['hotel_name']; ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
      .mt-20{
          margin-top:70px;
      }
      .room-card {
          border: 1px solid #ddd;
          border-radius: 8px;
          padding: 20px;
          margin-bottom: 20px;
          transition: transform 0.3s ease-in-out;
      }
      .room-card:hover {
          transform: scale(1.05);
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      }
      .room-price {
          font-size: 1.25rem;
          font-weight: bold;
          color: #007bff;
      }
      .room-status {
          font-weight: bold;
          color: green;
      }
      .btn-book {
          background-color: #007bff;
          color: white;
          font-weight: bold;
      }
      .btn-book:hover {
          background-color: #0056b3;
      }
  </style>
      <link rel="icon" href="img/door.png">

</head>

<body>
    
    <?php
    include 'user_navbar.php';
    ?>

  <div class="container mt-20">
    <h2 class="text-center mb-4">Rooms in <?php echo $hotel['hotel_name']; ?></h2>

    <?php if ($rooms): ?>
      <div class="row">
        <?php foreach ($rooms as $room): ?>
          <div class="col-md-4">
            <div class="room-card shadow-sm">
              <h5 class="mb-2"><?php echo $room['room_type']; ?></h5>
              <p class="room-price">Price: $<?php echo $room['price']; ?></p>
              <p class="room-status">Status: <?php echo $room['status']; ?></p>
              <a href="book_room.php?room_id=<?php echo $room['id']; ?>&hotel_id=<?php echo $hotel_id; ?>" class="btn btn-book w-100">Book this Room</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center">No rooms available in this hotel.</p>
    <?php endif; ?>

  </div>

</body>

</html>
