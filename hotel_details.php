<?php
session_start();
include 'db.php';
// Review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $hotel_id = intval($_POST['hotel_id']);
    $user_id = intval($_SESSION['user_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO reviews (hotel_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$hotel_id, $user_id, $rating, $comment]);
    } else {
        echo "<p class='text-danger'>Invalid review data.</p>";
    }
}


// getting the hotel details
if (!isset($_GET['id'])) {
    die("Hotel ID is required.");
}
$hotel_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT hotels.*, countries.name AS country_name FROM hotels 
                       INNER JOIN countries ON hotels.country_id = countries.id 
                       WHERE hotels.id = :hotel_id");
$stmt->execute(['hotel_id' => $hotel_id]);
$hotel = $stmt->fetch();

//Retrieving Reviews 

$stmt_reviews = $pdo->prepare("SELECT reviews.*, users.name AS user_name FROM reviews
INNER JOIN users ON reviews.user_id = users.id
WHERE hotel_id = :hotel_id");
$stmt_reviews->execute(['hotel_id' => $hotel_id]);
$reviews = $stmt_reviews->fetchAll();

//Retrieving hotel images 


$stmt_images = $pdo->prepare("SELECT image_path FROM hotel_images WHERE hotel_id = :hotel_id");
$stmt_images->execute(['hotel_id' => $hotel_id]);
$images = $stmt_images->fetchAll();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <link rel="icon" href="img/door.png">
    
<!-- Force fresh CSS load by adding version parameter -->
<link href="style/style.css?v=<?= time() ?>" rel="stylesheet">

</head>
<body>
    <?php include 'user_navbar.php'; ?>

    <div class="container mt-5 p-4 bg-light rounded shadow">
        <!--main-->
        <div class="row">
            
            <!--first-column : hotel details-->
            
            <div class="col-md-8">
                <h2 class="mb-3"> <?php echo $hotel['hotel_name']; ?> </h2>
                <p><strong>Description:</strong> <?php echo $hotel['description']; ?></p>
                <p><strong>Country:</strong> <?php echo $hotel['country_name']; ?></p>
                
                 <h4>Rooms</h4>
        <a href="rooms.php?hotel_id=<?php echo $hotel_id; ?>" class="btn btn-info ">View Rooms</a>
        
            
                <h4 class="mt-4">Hotel Location on Map</h4>
                <div id="map" class="rounded" style="height: 300px;"></div>
            </div>
            
            <!--second-column carousel images-->
            
            <div class="col-md-4">
                <!--id to link the carousel buttons-->
                <div id="carouselHotelImages" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $image): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                <img src="hotel/<?php echo $image['image_path']; ?>" class="d-block w-100 rounded" alt="Hotel Image">

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!--carousel buttons-->
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselHotelImages" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselHotelImages" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <hr>

       
        <hr>
<!--review section-->
<h4>Reviews</h4>
<?php if ($reviews): ?>
  <?php foreach ($reviews as $review): ?>
    <div class="card mb-3">
      <div class="card-body">
        <p><strong><?php echo $review['user_name']; ?></strong></p>
       
        <p>⭐ <?php echo $review['rating']; ?> Stars</p>
        <p><?php echo $review['comment']; ?></p>
         <p class="text-muted"><?php echo $review['created_at']; ?></p>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>No reviews yet.</p>
<?php endif; ?>

        <hr>

        <h4>Write a Review</h4>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <form method="POST">
                <input type="hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select name="rating" class="form-select" required>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        <?php } else { ?>
            <p>You need to <a href="login.php">log in</a> to write a review.</p>
        <?php } ?>
    </div>
<!--map settings-->
    <script>
        var map = L.map('map').setView([<?php echo $hotel['latitude']; ?>, <?php echo $hotel['longitude']; ?>], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(map);
        L.marker([<?php echo $hotel['latitude']; ?>, <?php echo $hotel['longitude']; ?>]).addTo(map)
            .bindPopup("<?php echo $hotel['hotel_name']; ?>")
            .openPopup();
    </script>

</body>
</html>
