<?php
session_start();

require_once __DIR__ . '/utils/csrf.php';

$prefilledEmail = $_GET['email'] ?? '';
$selectedPlan = $_GET['plan'] ?? 'none';

if (isset($_SESSION['user_id']) || $prefilledEmail === '') {
    header('Location: index.php');
    exit;
}

$errors = [
  'empty' => 'Please fill in all required fields.',
  'email' => 'Please enter a valid email address.',
  'exists' => 'That username or email is already registered.',
  'password' => 'Passwords do not match.',
  'weakpassword' => 'Password must contain at least 4 characters.',
];

$errorMessage = $errors[$_GET['error'] ?? ''] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Gym4U</title>
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
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="hamburger">☰</label>
    <nav class="right" aria-label="Main navigation">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="equipment.php">Equipment</a></li>
        <li><a href="login.php">Login</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="wrapper">
      <div class="card">
        <h2>Register</h2>
        <?php if ($errorMessage): ?>
          <p class="form-error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <form class="form" method="post" action="actions/register_action.php">

          <input type="hidden" name="subscription_plan" value="<?= htmlspecialchars($selectedPlan) ?>">

          <label for="name">Name</label>
          <input type="text" id="name" name="name" placeholder="Enter your name" required>

          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>

          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?= htmlspecialchars($prefilledEmail) ?>" readonly required>

          <label for="phone">Phone</label>
          <input type="tel" id="phone" name="phone" pattern="[0-9]{9}" placeholder="999999999" required>

          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>

          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Must have at least 4 characters" required>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

          <button type="submit" class="btn btn-enroll">Register</button>
          <p>Already have an account? <a href="login.php">Login here</a></p>

        </form>
      </div>
    </div>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancelations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>
