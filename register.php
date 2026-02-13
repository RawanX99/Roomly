<?php
session_start();
include 'db.php';
// form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password=$_POST['password'];
    if(!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $error="Password must contain at least one symbol(!@#$%^&*)";
    }else{
  $name = $_POST['name'];
  $pass = password_hash($password, PASSWORD_DEFAULT);
  $email = $_POST['email'];
  $user_type = $_POST['user_type']; // get the user type

  // prepare sql query to insert the new user
  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, created_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->execute([$name, $email, $pass, $user_type]);

  $_SESSION['user_id'] = $pdo->lastInsertId(); // تخزين الـ user_id في الجلسة
  header("Location: login.php"); 
  exit(); // مهم لإنهاء تنفيذ السكريبت بعد إعادةالتوجيه
    }
        
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="style/style.css">

  <link rel="icon" href="img/door.png">

</head>
<body class="back_login">

  <div class="container mt-5">
      <div class="box-login">
          
          
    <h2>Create a New Account</h2>
    <?php if(isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    } ?>
    <form action="register.php" method="POST">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label for="user_type" class="form-label">User Type</label>
        <select name="user_type" class="form-control" required>
          <option value="0">Visitor (User)</option>
          <option value="1">Hotel Owner</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <div class="mt-3">
      <a href="login.php">Do you have an account? Login </a>
    </div>
  </div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
