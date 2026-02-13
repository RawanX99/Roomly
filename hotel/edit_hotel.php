<?php
session_start();
include '../db.php';

if ($_SESSION['user_type'] != 1) {
    header("Location: login.php");
    exit();
}

$hotel_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $hotel_id, 'user_id' => $_SESSION['user_id']]);
$hotel = $stmt->fetch();

if (!$hotel) {
    header("Location: hotel_home.php");
    exit();
}

$countries_stmt = $pdo->query("SELECT id, name FROM countries");
$countries = $countries_stmt->fetchAll(PDO::FETCH_ASSOC);

// form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hotel_name = $_POST['hotel_name'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $country_id = $_POST['country'];

    $stmt = $pdo->prepare("UPDATE hotels SET hotel_name = ?, description = ?, latitude = ?, longitude = ?, country_id = ? WHERE id = ?");
    $stmt->execute([$hotel_name, $description, $latitude, $longitude, $country_id, $hotel_id]);

    header("Location: hotel_home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../style/style.css?v=<?= time() ?>" rel="stylesheet">
      <link rel="icon" href="../img/door.png">

    <style>
        body {
            background-color: #f8f9fa;
        }

        #mainContainer {
            max-width: 700px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #343a40;
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include 'hotel_navbar.php'; ?>

    <div class="container mt-5" id="mainContainer">
        <h2>Edit Hotel</h2>
        <form method="POST">
            <div class="mb-3">
                <!--previous hotel details-->
                <label for="name" class="form-label">Hotel Name</label>
                <input type="text" name="hotel_name" class="form-control" value="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Hotel Description</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($hotel['description']); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="<?php echo htmlspecialchars($hotel['latitude']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="<?php echo htmlspecialchars($hotel['longitude']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">City</label>
                <select name="country" class="form-select" required>
                    <?php foreach ($countries as $country): ?>
                        <!--previous hotel details-->
                        <option value="<?php echo $country['id']; ?>" <?php
                        // only hotel city
                        echo ($hotel['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($country['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
