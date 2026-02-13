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
  die("You are not authorized to view this hotel's reviews.");
}

$stmt = $pdo->prepare("
    SELECT 
        reviews.id AS review_id, 
        reviews.rating, 
        reviews.comment, 
        reviews.created_at, 
        users.name
    FROM reviews
    JOIN users ON reviews.user_id = users.id
    WHERE reviews.hotel_id = :hotel_id
");
$stmt->execute(['hotel_id' => $hotel_id]);
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Reviews</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../style/style.css?v=<?= time() ?>">
      <link rel="icon" href="../img/door.png">

</head>

<body>
  <?php include 'hotel_navbar.php'; ?>

  <div class="container mt-5">
    <h2>Reviews for <?php echo $hotel['hotel_name']; ?></h2>

    <?php if (count($reviews) > 0): ?>
      <table class="table table-bordered mt-4">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reviews as $review): ?>
            <tr>
              <td><?php echo $review['review_id']; ?></td>
              <td><?php echo $review['name']; ?></td>
              <td><?php echo $review['rating']; ?>/5</td>
              <td><?php echo $review['comment']; ?></td>
              <td><?php echo $review['created_at']; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No reviews available for this hotel.</p>
    <?php endif; ?>

    <a href="hotel_home.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>