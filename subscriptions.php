<?php
session_start();

$selectedPlan = $_GET['plan'] ?? null;

$planNames = [
  'offpeak' => 'Off-Peak Plan',
  'classic' => 'Classic Plan',
  'premium' => 'Premium Plan',
];
?>

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
    <section class="subscriptions-hero">
      <h3 class="section-title-1">1. Choose Your Membership</h3>
      <h3 class="section-title-2">2. Create an Account</h3>
      <h3 class="section-title-3">3. Start Training!</h3>
    </section>
    <section class="subscriptions-grid">
      <article class="section about">
        <h3>Off-Peak Plan</h3>
        <p class="subscription-price">€19.99/month</p>
        <ul>
          <li>Access to gym equipment</li>
          <li>Locker room access</li>
          <li>1 group class per week</li>
          <li>Free healthy drinks</li>
          <li>Restricted time access</li>
        </ul>
        <a href="subscriptions.php?plan=offpeak" class="btn-primary">Select Plan</a>
      </article>
      <article class="section about featured">
        <div class="subscription-badge">Most Popular</div>
        <h3>Classic Plan</h3>
        <p class="subscription-price">€29.99/month</p>
        <ul>
          <li>All Off-Peak Plan benefits</li>
          <li>Unlimited group classes</li>
          <li>Access to all equipment</li>
          <li>Priority booking</li>
          <li>Nutrition evaluation included</li>
        </ul>
        <a href="subscriptions.php?plan=classic" class="btn-primary">Select Plan</a>
      </article>
      <article class="section about">
        <h3>Premium Plan</h3>
        <p class="subscription-price">€39.99/month</p>
        <ul>
          <li>All Classic Plan benefits</li>
          <li>Unlimited gym access</li>
          <li>Unlimited classes</li>
          <li>Personal trainer sessions</li>
          <li>Exclusive wellness benefits</li>
        </ul>
        <a href="subscriptions.php?plan=premium" class="btn-primary">Select Plan</a>
      </article>
    </section>
    <section class="subscriptions-login">
      <p>Already a Gym4U member?</p>
      <a href="login.php">Login Here</a>
    </section>
    <div class="payment-overlay" style="<?= $selectedPlan ? 'display:flex;' : 'display:none;' ?>">
      <div class="section about">
        <a href="subscriptions.php" class="close-modal">×</a>
        <h2>Complete Your Subscription</h2>
        <?php if ($selectedPlan): ?>
            <h3 class="section-title"><strong><?= htmlspecialchars(ucfirst($planNames[$selectedPlan] ?? '')) ?></strong></h3>
        <?php endif; ?>
        <form method="get" action="register.php">
          <div class="subscription-info">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="your.email@example.com" required>

            <label for="cardholder">Cardholder</label>
            <input type="text" id="cardholder" placeholder="John Doe">

            <label for="cardnumber">Card</label>
            <input type="text" id="cardnumber" placeholder="1234 5678 9012 3456">

            <label for="expiry">Expire</label>
            <input type="text" id="expiry" placeholder="MM/YY">

            <label for="cvv">CVV</label>
            <input type="password" id="cvv" placeholder="123">
          </div>

          <input type="hidden" name="plan" value="<?= htmlspecialchars($selectedPlan ?? '') ?>">

          <button type="submit" class="btn-primary">Register Page</button>
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
</body>
</html>