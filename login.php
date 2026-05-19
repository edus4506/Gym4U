<?php
session_start();
require_once __DIR__ . '/utils/csrf.php';

require_once __DIR__ . '/utils/csrf.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [
  'empty' => 'Please enter your username and password.',
  'invalid' => 'Invalid username or password.',
];

$errorMessage = $errors[$_GET['error'] ?? ''] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Gym4U</title>
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/authentication.css">
</head>
<body>
  <header>
    <div>
      <a href="index.php">
        <img src="images/gym4u_logo.png" alt="Gym4U logo" class="logo">
      </a>
    </div>
    <div class="middle"><h1>Gym4U</h1></div>
    <nav class="right" aria-label="Main navigation">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="equipment.php">Equipment</a></li>
        <li><a href="login.php" aria-current="page">Login</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="wrapper">
      <div class="card">
        <h2>Login</h2>
        <?php if ($errorMessage): ?>
          <p class="form-error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form class="form" method="post" action="actions/login_action.php">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>

          <label for="password">Password</label>

          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <span class="eye-icon" id="eye-icon"> 👁️ </span>
          </div>

          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <button type="submit" class="btn btn-enroll">Login</button>
          <p>Don't have an account? <a href="register.php">Register here</a></p>
        </form>
      </div>
    </div>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="#">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
  <script src="js/main.js"></script>
  <script src="js/authentication.js"></script>
</body>
</html>
