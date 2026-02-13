<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

if (!isset($_GET['id'])) {
  die("Hotel ID is required.");
}

$hotel_id = $_GET['id'];

//get the hotel info based on the owner id and hotel id

$stmt_check = $pdo->prepare("SELECT * FROM hotels WHERE id = :hotel_id AND user_id = :user_id");
$stmt_check->execute(['hotel_id' => $hotel_id, 'user_id' => $_SESSION['user_id']]);
$hotel = $stmt_check->fetch();

if (!$hotel) {
  die("You are not authorized to manage this hotel.");
}

// delete a room
// check if the delete room submitted by(hidden element)
if (isset($_POST['delete_room'])) {
  $room_id = $_POST['room_id'];
// delete bookings first
  $deleteBookings = $pdo->prepare("DELETE FROM bookings WHERE room_id = :room_id");
  $deleteBookings->execute(['room_id' => $room_id]);
  
  // delete the room
  $deleteStmt = $pdo->prepare("DELETE FROM rooms WHERE id = :room_id AND hotel_id = :hotel_id");
  $deleteStmt->execute(['room_id' => $room_id, 'hotel_id' => $hotel_id]);
  
  header("Location: view_rooms.php?id=".$hotel_id);
  exit();
}

// edit or update a room
if (isset($_POST['update_room'])) {
  $room_id = $_POST['room_id'];
  $room_type = $_POST['room_type'];
  $price = $_POST['price'];
  
  $updateStmt = $pdo->prepare("
    UPDATE rooms 
    SET room_type = :room_type, price = :price 
    WHERE id = :room_id AND hotel_id = :hotel_id
  ");
  $updateStmt->execute([
    'room_type' => $room_type,
    'price' => $price,
    'room_id' => $room_id,
    'hotel_id' => $hotel_id
  ]);
//   refresh
  header("Location: view_rooms.php?id=".$hotel_id);
  exit();
}

// get the rooms details
$stmt_rooms = $pdo->prepare("
  SELECT id, room_type, price, status, 
         booked_from, booked_to 
  FROM rooms 
  WHERE hotel_id = :hotel_id
");
$stmt_rooms->execute(['hotel_id' => $hotel_id]);
$rooms = $stmt_rooms->fetchAll();

//if the booking date has passed
foreach ($rooms as $room) {
  if ($room['status'] == 'booked' && strtotime($room['booked_to']) < time()) {
    $updateStmt = $pdo->prepare("
      UPDATE rooms 
      SET status = 'available', booked_from = NULL, booked_to = NULL 
      WHERE id = :room_id
    ");
    $updateStmt->execute(['room_id' => $room['id']]);
  }
}

// to diplay added room message
if (isset($_GET['added'])) {
  $success_message = "Room added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Rooms Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style/style.css?v=<?= time() ?>">
        <link rel="icon" href="../img/door.png">

</head>

<body>
  <?php include 'hotel_navbar.php'; ?>

  <div class="container mt-5">
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <h2>Manage Rooms for <?php echo htmlspecialchars($hotel['hotel_name']); ?></h2>

    <!-- Display Rooms Section -->
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span>Rooms</span>
        <a href="add_room.php?id=<?php echo $hotel_id; ?>" class="btn btn-success btn-sm">Add New Room</a>
      </div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Room Type</th>
              <th>Price</th>
              <th>Status</th>
              <th>Booking Dates</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rooms as $room): ?>
              <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type']); ?></td>
                <td><?php echo htmlspecialchars($room['price']); ?></td>
                <td><?php echo ucfirst($room['status']); ?></td>
                <td>
                  <?php if ($room['status'] == 'booked'): ?>
                    From: <?php echo htmlspecialchars($room['booked_from']); ?><br>
                    To: <?php echo htmlspecialchars($room['booked_to']); ?>
                  <?php else: ?>
                    Available
                  <?php endif; ?>
                </td>
                <td>
                  <!--edit & delete buttons-->
                  <!--open a modal-->
                  <button class="btn btn-warning btn-sm" data-bs-toggle="modal" 
                          data-bs-target="#editModal" 
                          data-roomid="<?php echo $room['id']; ?>"
                          data-roomtype="<?php echo htmlspecialchars($room['room_type']); ?>"
                          data-price="<?php echo htmlspecialchars($room['price']); ?>">
                    Edit
                  </button>
                  
                  <button class="btn btn-danger btn-sm" data-bs-toggle="modal" 
                          data-bs-target="#deleteModal" 
                          data-roomid="<?php echo $room['id']; ?>">
                    Delete
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">

          <input type="hidden" name="room_id" id="edit_room_id">
          <input type="hidden" name="update_room" value="1">
          
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="edit_room_type" class="form-label">Room Type</label>
              <input type="text" class="form-control" id="edit_room_type" name="room_type" required>
            </div>
            <div class="mb-3">
              <label for="edit_price" class="form-label">Price</label>
              <input type="number" step="0.1" class="form-control" id="edit_price" name="price" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
            <!--room id-->
          <input type="hidden" name="room_id" id="delete_room_id">
          <!--delete form submission-->
          <input type="hidden" name="delete_room" value="1">
          
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this room? This action cannot be undone.</p>
            <p class="text-danger"><strong>Warning:</strong> Any existing bookings for this room will also be deleted.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete Room</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // edit modal script
      var editModal = document.getElementById('editModal');
      editModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var roomId = button.getAttribute('data-roomid');
        var roomType = button.getAttribute('data-roomtype');
        var price = button.getAttribute('data-price');
        
        document.getElementById('edit_room_id').value = roomId;
        document.getElementById('edit_room_type').value = roomType;
        document.getElementById('edit_price').value = price;
      });
    //   delete modal script
      var deleteModal = document.getElementById('deleteModal');
      deleteModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        var roomId = button.getAttribute('data-roomid');
        document.getElementById('delete_room_id').value = roomId;
      });

      // room added sucsess message
      var alert = document.querySelector('.alert');
      if (alert) {
        setTimeout(function() {
          alert.classList.remove('show');
          alert.classList.add('fade');
        }, 10000);
      }
    });
  </script>
</body>
</html>