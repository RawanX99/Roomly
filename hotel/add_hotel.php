<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // store the data from the post request 
  $name = $_POST['name'];
  $description = $_POST['description'];
  $latitude = $_POST['latitude'];
  $longitude = $_POST['longitude'];
  $country_id = $_POST['country_id'];
  $user_id = $_SESSION['user_id'];

  // check the country_id
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM countries WHERE id = ?");
  $stmt->execute([$country_id]);
  if ($stmt->fetchColumn() == 0) {
    die("Invalid country selected.");
  }

  //enter hotel details
$stmt = $pdo->prepare("INSERT INTO hotels (hotel_name, description, latitude, longitude, country_id, user_id, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())");

  $stmt->execute([$name, $description, $latitude, $longitude, $country_id, $user_id]);
  
  $hotel_id = $pdo->lastInsertId();

  // check the element image
  if (!empty($_FILES['images']['name'][0])) {
    $upload_dir = 'uploads/hotels/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0777, true); // create directories
      
        
    }

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
      $file_name = $_FILES['images']['name'][$key];
      $file_tmp = $tmp_name;
      $file_type = mime_content_type($file_tmp); // image type



      // check if it is an image to construct a new path and name for images
      if (str_starts_with($file_type, 'image/')) {
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);//image.ext
        $unique_name = uniqid() . '.' . $file_extension; //unique name for image
        $file_path = $upload_dir . $unique_name;

        // upload to the new path
        if (move_uploaded_file($file_tmp, $file_path)) {
          // hotel_images table
          $stmt = $pdo->prepare("INSERT INTO hotel_images (hotel_id, image_path) VALUES (?, ?)");
          $stmt->execute([$hotel_id, $file_path]);
        } else {
          echo "Failed to upload image: $file_name";
        }
      } else {
        echo "Invalid file type: $file_name";
      }
    }
  }

  header("Location: hotel_home.php");
  exit();
}
// the countries table
$stmt = $pdo->query("SELECT * FROM countries");
$countries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
          <link rel="icon" href="../img/door.png">

</head>

<body>
  <div class="container mt-5">
    <h2>Add Hotel</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="name" class="form-label">Hotel Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" required></textarea>
      </div>

      <div class="mb-3">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="text" name="latitude" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="text" name="longitude" class="form-control" required>
      </div>

      <div class="mb-3">
          
        <label for="country" class="form-label">City</label>
        
        <select name="country_id" class="form-control" required>
          <option value="">Select City</option>
          <?php foreach ($countries as $country): ?>
          
            <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
            
          <?php endforeach; ?>
        </select>
        
      </div>

      <div class="mb-3">
        <label for="images" class="form-label">Upload Hotel Images</label>
        <input type="file" class="form-control" id="images" name="images[]" multiple>
      </div>

      <button type="submit" class="btn btn-primary">Add Hotel</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>