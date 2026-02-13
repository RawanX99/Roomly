<?php
session_start();
include 'db.php';
// not authorized
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 0) {
    header("Location: login.php");
    exit();
}

// get hotels and countries
$stmt = $pdo->prepare("
    SELECT hotels.*, countries.name AS country_name 
    FROM hotels
    LEFT JOIN countries ON hotels.country_id = countries.id
");
$stmt->execute();
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// hotel images
$imageStmt = $pdo->prepare("SELECT hotel_id, image_path FROM hotel_images");
$imageStmt->execute();
$imageResults = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

// sort images
$images = [];
foreach ($imageResults as $img) {
    $images[$img['hotel_id']][] = $img['image_path'];
}

// countries for filtering 
$countryStmt = $pdo->query("SELECT id, name FROM countries");
$countries = $countryStmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">



  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Swiper -->
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="icon" href="img/door.png">

  <style>
    .mt-7 {
      margin-top: 70px;
    }
    .swiper-container {
      width: 100%;
      height: 250px;
    }
    #map {
      height: 400px;
      margin-top: 30px;

    }
    .swiper-pagination-bullet {
  
    width: 10px; 
    height: 10px; 
}
  </style>
</head>
<body>

    <?php include 'user_navbar.php'; ?>

<div class="container mt-7">
    <!--main-->
  <div class="row">
      <!--first columns: sidebar-->
    <div class="col-md-3">
        
      <div class="card p-3" style="position: sticky; top: 70px;">
        <h5 class="card-title">Filter & Search</h5>
        
        <!--filtering by country-->
        <div class="mb-3">
          <label for="countryFilter" class="form-label">Filter by City:</label>
          <select class="form-select" id="countryFilter">
            <option value="all">All cities</option>
            <?php foreach ($countries as $country): ?>
              <option value="<?= htmlspecialchars($country['id']); ?>">
                <?= htmlspecialchars($country['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <!--searching by name-->
        <div class="mb-3">
          <label for="hotelSearch" class="form-label">Search Hotels:</label>
          <input type="text" class="form-control" id="hotelSearch" placeholder="Enter hotel name...">
        </div>
        
        
      </div>
    </div>
    <!--second columns: hotels-->
    <div class="col-md-9">
        
      <div class="row" id="hotelList">
          
        <?php foreach ($hotels as $hotel): ?>
          <div class="col-md-4 hotel-item mb-4" data-country="<?= htmlspecialchars($hotel['country_id']); ?>">
              
            <div class="card h-100">
              
              <div class="swiper-container">
                <div class="swiper-wrapper" >
                    <!--check the hotel images-->
                  <?php if (isset($images[$hotel['id']])): ?>
                    <?php foreach ($images[$hotel['id']] as $imagePath): ?>
                      <div class="swiper-slide">
                        <img src="hotel/<?= htmlspecialchars($imagePath); ?>" class="card-img-top swiper-lazy" style="height: 250px; object-fit: cover;" alt="Hotel Image">
                      </div>
                    <?php endforeach; ?>
                    
                  <?php else: ?>
                    <div class="swiper-slide">
                      <img src="default-image.jpg" class="card-img-top swiper-lazy" alt="No Image">
                    </div>
                  <?php endif; ?>
                </div>
                
                
                <!-- Pagination and Preloader -->
                <div class="swiper-pagination" ></div>
                

               
              </div>
              
              
              <!--hotel details-->
              <div class="card-body">
                  <div class="dis-f">
                      
                      <div class="up">
                              
                      <h5 class="card-title hotel-name"><?= htmlspecialchars($hotel['hotel_name']); ?></h5>
                      <p class="card-text text-muted"><?= htmlspecialchars(substr($hotel['description'], 0, 50)) . '...'; ?></p>
                
                      </div>
                      
                  
                  
                      <div class="down">
                      
                <p class="card-text"><small class="text-muted"><?= htmlspecialchars($hotel['country_name']); ?></small></p>
                <a href="hotel_details.php?id=<?= $hotel['id']; ?>" class="btn btn-primary">View Details</a>
                
                      </div>
                      
            
                
                
                  </div>
                
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <h4 class="mt-5 mb-3">Hotels on the Map</h4>
      <div id="map"></div>
    </div>
  </div>
</div>

<!-- JS Scripts -->
<script>
  //country selection list
  document.getElementById('countryFilter').addEventListener('change', function () {
    const selectedCountry = this.value;
    const hotelItems = document.querySelectorAll('.hotel-item');
    hotelItems.forEach(item => {
      const hotelCountry = item.getAttribute('data-country');
      item.style.display = (selectedCountry === 'all' || hotelCountry === selectedCountry) ? 'block' : 'none';
    });
  });

  // searching by the hotel name
  document.getElementById('hotelSearch').addEventListener('keyup', function () {
    const searchQuery = this.value.toLowerCase();
    const hotelItems = document.querySelectorAll('.hotel-item');
    hotelItems.forEach(item => {
      const hotelName = item.querySelector('.hotel-name').textContent.toLowerCase();
      item.style.display = hotelName.includes(searchQuery) ? 'block' : 'none';
    });
  });

  // map settings
const map = L.map('map').setView([21.4858, 39.1925], 10); 
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  <?php foreach ($hotels as $hotel): ?>
    <?php if (!empty($hotel['latitude']) && !empty($hotel['longitude'])): ?>
      L.marker([<?= $hotel['latitude']; ?>, <?= $hotel['longitude']; ?>])
        .addTo(map)
        .bindPopup("<?= htmlspecialchars($hotel['hotel_name']); ?>");
    <?php endif; ?>
  <?php endforeach; ?>

  // Swiper
  document.querySelectorAll('.swiper-container').forEach(function (container) {
  const slides = container.querySelectorAll('.swiper-slide');
  
  // Check number of slides
  const slidesCount = slides.length;

  new Swiper(container, {
    spaceBetween: 10,
    slidesPerView: 1,
    loop: slidesCount > 1, // Enable loop only if there's more than one slide
    lazy: true,
    autoplay: {
      delay: 2500,
      disableOnInteraction: false
    },
    effect: 'fade',
    pagination: {
      el: container.querySelector('.swiper-pagination'),
      clickable: true,
    },
    on: {
      init: function() {
        console.log('Swiper Initialized!');
      },
    }
  });
});
</script>

</body>
</html>
