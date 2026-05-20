<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscriptions | Gym4U</title>
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/subscriptions.css">
</head>
<body>
  <header>
    <div>
      <a href="index.php">
        <img src="images/gym4u_logo.png" alt="Gym4U logo" class="logo">
      </a>
    </div>
    <div class="middle">
      <h1>Gym4U</h1>
    </div>
    <nav class="right" aria-label="Main navigation">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="equipment.php">Equipment</a></li>
        <li><a href="subscriptions.php" aria-current="page">Subscriptions</a></li>

        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="actions/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
        <?php endif; ?>
        
      </ul>
    </nav>
  </header>
  <main>
    <section class="subscriptions-hero">
      <h3 class="section-title-1">1. Choose Your Membership</h3>
      <h3 class="section-title-2">2. Create an Account</h3>
      <h3 class="section-title-3">3. Start Training!</h3>
    </section>
    <section class="subscriptions-grid">
      <article class="subscription-card">
        <h3>Off-Peak Plan</h3>
        <p class="subscription-price">€19.99/month</p>
        <ul>
          <li>Access to gym equipment</li>
          <li>Locker room access</li>
          <li>1 group class per week</li>
          <li>Free healthy drinks</li>
          <li>Restricted access during peak hours</li>
        </ul>
        <a href="#" class="btn-primary">Subscribe</a>
      </article>
      <article class="subscription-card featured">
        <div class="subscription-badge">Most Popular</div>
        <h3>Total Plan</h3>
        <p class="subscription-price">€29.99/month</p>
        <ul>
          <li>Unlimited group classes</li>
          <li>Access to all equipment</li>
          <li>Priority booking</li>
          <li>Nutrition evaluation included</li>
        </ul>
        <a href="#" class="btn-primary">Subscribe</a>
      </article>
      <article class="subscription-card">
        <h3>Premium Plan</h3>
        <p class="subscription-price">€39.99/month</p>
        <ul>
          <li>Unlimited gym access</li>
          <li>Unlimited classes</li>
          <li>Personal trainer sessions</li>
          <li>Exclusive wellness benefits</li>
        </ul>
        <a href="#" class="btn-primary">Subscribe</a>
      </article>
    </section>
    <section class="subscriptions-login">
      <h4>Already a Gym4U member?</h4>
      <a href="login.php" class="btn-primary">Login Here</a>
    </section>
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
</body>
</html>