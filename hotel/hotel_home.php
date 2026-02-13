<?php
session_start();
include '../db.php';

// check the user type
if ($_SESSION['user_type'] != 1) {
  header("Location: login.php");
  exit();
}

//retrieve hotel row 
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE user_id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$hotel = $stmt->fetch();

// check hotel owner registeration
if (!$hotel) {
  echo "You don't have a hotel listed yet. Please <a href='add_hotel.php'>add hotel</a>.";
  exit();
}

$hotel_id = $hotel['id'];
$hotel_name = $hotel['hotel_name'];

// get hotel images
$stmt_images = $pdo->prepare("SELECT * FROM hotel_images WHERE hotel_id = :hotel_id");
$stmt_images->execute(['hotel_id' => $hotel_id]);
$images = $stmt_images->fetchAll();  

// count hotel rooms
$stmt_rooms = $pdo->prepare("SELECT COUNT(*) AS total_rooms FROM rooms WHERE hotel_id = :hotel_id");
$stmt_rooms->execute(['hotel_id' => $hotel_id]);
$total_rooms = $stmt_rooms->fetch()['total_rooms'];

// bring only the available rooms
$stmt_available_rooms = $pdo->prepare("SELECT COUNT(*) AS available_rooms FROM rooms WHERE hotel_id = :hotel_id AND status = 'available'");
$stmt_available_rooms->execute(['hotel_id' => $hotel_id]);
$available_rooms = $stmt_available_rooms->fetch()['available_rooms'];

// get recent bookings from bookings table
$stmt_recent_bookings = $pdo->prepare("
    SELECT b.id, b.check_in_date, b.check_out_date, b.status, 
           r.room_type, r.price,
           u.name as guest_name
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN users u ON b.user_id = u.id
    WHERE b.hotel_id = :hotel_id
    ORDER BY b.created_at DESC 
    LIMIT 5
");
$stmt_recent_bookings->execute(['hotel_id' => $hotel_id]);
$recent_bookings = $stmt_recent_bookings->fetchAll();

// get reviews
$stmt_recent_reviews = $pdo->prepare("SELECT * FROM reviews WHERE hotel_id = :hotel_id ORDER BY created_at DESC LIMIT 5");
$stmt_recent_reviews->execute(['hotel_id' => $hotel_id]);
$recent_reviews = $stmt_recent_reviews->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../style/style.css?v=<?= time() ?>" rel="stylesheet">
  <link rel="icon" href="../img/door.png">
  <style>
    .booking-table {
      font-size: 0.9rem;
    }
    .booking-table th {
      white-space: nowrap;
    }
    /*.status-pending {*/
    /*  color: #ffc107;*/
    /*  font-weight: bold;*/
    /*}*/
    /*.status-confirmed {*/
    /*  color: #28a745;*/
    /*  font-weight: bold;*/
    /*}*/
    /*.status-cancelled {*/
    /*  color: #dc3545;*/
    /*  font-weight: bold;*/
    /*}*/
  </style>
</head>

<body>
  <?php include 'hotel_navbar.php'; ?>
  <div class="container mt-3">
    <div class="row mt-4">
      <!--total and available cards-->
      <div class="col-md-3">
        <div class="card text-center bg-light">
          <div class="card-body">
            <h5 class="card-title">Total Rooms</h5>
            <p class="card-text display-6"><?php echo $total_rooms; ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center bg-light">
          <div class="card-body">
            <h5 class="card-title">Available Rooms</h5>
            <p class="card-text display-6"><?php echo $available_rooms; ?></p>
          </div>
        </div>
      </div>

      <!--carousel images-->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Hotel Images</div>
          <div class="card-body">
            <div id="hotelImagesCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                <?php if ($images): ?>
                  <?php $first = true; ?>
                  <?php foreach ($images as $image): ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                      <img src="../hotel/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Hotel Image" class="d-block w-100" style="height:350px; object-fit: cover;">
                    </div>
                    <?php $first = false; ?>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p>No images available</p>
                <?php endif; ?>
              </div>
              <!--carousel controls-->
              <button class="carousel-control-prev" type="button" data-bs-target="#hotelImagesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#hotelImagesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!--recent bookings and reviews list-->
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span>Recent Bookings</span>
              <a href="hotel_bookings.php?id=<?php echo $hotel_id; ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <!--scrollable table-->
              <div class="table-responsive">
                <table class="table table-hover booking-table mb-0">
                  <thead>
                    <tr>
                      <!--<th>ID</th>-->
                      <th>Guest</th>
                      <th>Room</th>
                      <th>Dates</th>
                      <!--<th>Status</th>-->
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($recent_bookings) > 0): ?>
                      <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                          <!--<td>#<?php echo $booking['id']; ?></td>-->
                          
                          
                          <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                          <td><?php echo htmlspecialchars($booking['room_type']); ?></td>
                          <td>
                            <small>
                              <?php echo date('M j', strtotime($booking['check_in_date'])); ?> - 
                              <?php echo date('M j', strtotime($booking['check_out_date'])); ?>
                            </small>
                          </td>
                          <td>
                              <!--not used commented out-->
                            <!--<span class="status-<?php echo strtolower($booking['status']); ?>">-->
                              <!--<?php echo ucfirst($booking['status']); ?>-->
                            </span>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="5" class="text-center py-3">No recent bookings found</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">Recent Reviews</div>
            <div class="card-body">
              <?php if (count($recent_reviews) > 0): ?>
                <div class="list-group">
                  <?php foreach ($recent_reviews as $review): ?>
                    <div class="list-group-item">
                      <div class="d-flex justify-content-between">
                        <strong><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></strong>
                        <small class="text-muted"><?php echo date('M j, Y', strtotime($review['created_at'])); ?></small>
                      </div>
                      <p class="mb-0 mt-1"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No reviews yet</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>