<?php
session_start();
include 'db.php';
// subbmission form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];

    if ($user['user_type'] == 0) {
      header("Location: user_dashboard.php");
    } elseif ($user['user_type'] == 1) {
      header("Location: hotel/hotel_home.php");
    }
  } else {
    $error = "Invalid login credentials.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="img/door.png">

</head>
<link rel="stylesheet" href="style/style.css">

<body class="back_login">

  <div class="container mt-5 ">


    <div class="box-login">
      <h2>Login</h2>
      <!--submission error-->
      <?php if (isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
      } ?>
      <form method="POST">
        <div class="mb-3">
          <label for="email" class="form-label mt-5">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary but-hotel">Login</button>

        <div class="mt-5">
          <a href="register.php">Don't have an account? Sign Up</a>
        </div>
      </form>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>