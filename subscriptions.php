<?php require_once __DIR__ . '/controllers/subscriptionsController.php'; ?>

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
        <li><a href="profile.php">Profile</a></li>
        <li>
          <form action="actions/logout.php" method="post" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button type="submit">Logout</button>
          </form>
        </li>
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
      <article class="section subscription-card">
        <h3>Off-Peak Plan</h3>
        <p class="subscription-price">€19.99/month</p>
        <ul>
          <li>Access to gym equipment</li>
          <li>Locker room access</li>
          <li>2 classes per week</li>
          <li>Restricted time access</li>
          <li>Free healthy drinks</li>
        </ul>
        <a href="subscriptions.php?plan=offpeak" class="btn-primary">Select Plan</a>
      </article>
      <article class="section subscription-card featured">
        <span class="subscription-ribbon">Most Popular</span>
        <h3>Classic Plan</h3>
        <p class="subscription-price">€29.99/month</p>
        <ul>
          <li>All Off-Peak Plan benefits</li>
          <li>6 classes per week</li>
          <li>Unrestricted time access</li>
          <li>Free towel service</li>
        </ul>
        <a href="subscriptions.php?plan=classic" class="btn-primary">Select Plan</a>
      </article>
      <article class="section subscription-card">
        <h3>Premium Plan</h3>
        <p class="subscription-price">€39.99/month</p>
        <ul>
          <li>All Classic Plan benefits</li>
          <li>Unlimited classes</li>
          <li>Waitlist in full classes</li>
          <li>Dedicated locker</li>
          <li>Free merchandise</li>
        </ul>
        <a href="subscriptions.php?plan=premium" class="btn-primary">Select Plan</a>
      </article>
    </section>
    <div class="payment-overlay" style="<?= $selectedPlan ? 'display:flex;' : 'display:none;' ?>">
      <div class="section subscription-card">
        <a href="subscriptions.php" class="close-modal">×</a>
        <h2>Complete Your Subscription</h2>
        <?php if ($selectedPlan): ?>
          <h3 class="section-title"><strong><?= htmlspecialchars($planNames[$selectedPlan] ?? '') ?></strong></h3>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
          <p class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
          <p>Please register a new account first.</p>
          <a href="register.php" class="btn-primary">Register Account</a>

        <?php else: ?>
          <form method="post" action="actions/join_membership_action.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <div class="subscription-info">
              <label for="password">Confirm Password</label>
              <div class="password-wrapper">
                  <input type="password" name="password" placeholder="Enter your password" required>
                  <span class="eye-icon">👁️</span>
              </div>
              <label for="cardholder">Cardholder</label>
              <input type="text" id="cardholder" placeholder="John Doe" required>

              <label for="cardnumber">Card</label>
              <input type="text" id="cardnumber" placeholder="1234 5678 9012 3456" pattern="[0-9]{4} [0-9]{4} [0-9]{4} [0-9]{4}" maxlength="19" required>

              <label for="expiry">Expire</label>
              <input type="text" id="expiry" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/[0-9]{2}" maxlength="5" required>

              <label for="cvv">CVV</label>
              <input type="password" id="cvv" placeholder="123" pattern="[0-9]{3}" maxlength="3" required>
            </div>
            <input type="hidden" name="subscription_plan" value="<?= htmlspecialchars($selectedPlan ?? '') ?>">
            <button type="submit" class="btn-primary">Confirm Membership</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </main>
  <footer>
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancellations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
  <script src="js/authentication.js"></script>
</body>
</html>