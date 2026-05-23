<?php
declare(strict_types = 1);
session_start();

require_once __DIR__ . '/database/db.php';
require_once __DIR__ . '/utils/csrf.php';

$user = null;

if (isset($_SESSION['user_id'])) {
  $db = getDatabaseConnection();

  $stmt = $db->prepare('
    SELECT
      id,
      name,
      email,
      role,
      subscription_plan
    FROM users
    WHERE id = ?
  ');

  $stmt->execute([(int)$_SESSION['user_id']]);

  $user = $stmt->fetch() ?: null;
}

$errors = [
  'password' => 'Incorrect password.',
];

$errorMessage = $errors[$_GET['error'] ?? ''] ?? null;
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Cancellations | Gym4U</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/profile.css">
  <link rel="stylesheet" href="css/footers.css">
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
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="actions/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="subscriptions.php">Subscribe</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Cancellations</h2>
      <p>
        At Gym4U, we understand that plans can change. Members may cancel subscriptions and memberships directly through the platform at any time. Thank you for your understanding and cooperation in helping us maintain a fair and organized membership system for all users.
      </p>
    </section>

    <section class="section about">
      <h2>Cancel Classes or Personal Training Sessions</h2>
      <p>
        If you wish to cancel a class or personal training session, you can manage your cancellation directly through your profile page.
      </p>

      <?php if (isset($_SESSION['user_id'])) : ?>
        <a href="profile.php" class="btn-primary">Go to Profile</a>
      <?php else : ?>
        <a href="login.php" class="btn-primary">Login to Continue</a>
      <?php endif; ?>
    </section>

    <section class="section about">
      <h2>Membership Cancellation Policy</h2>

      <article class="feature">
        <h3>1. Membership Cancellation Requests</h3>

        <p>
          Gym4U understands that personal circumstances, schedule changes, financial situations, or lifestyle adjustments may occasionally require members to cancel their active memberships or subscription plans.
        </p>

        <p>
          Members may request membership cancellation directly through their profile area on the Gym4U platform. Cancellation requests should always be submitted before the next billing cycle to avoid additional charges.
        </p>

        <p>
          Once a cancellation request is submitted, the membership will remain active until the end of the current billing period. During this time, members may continue accessing gym facilities, classes, and services included in their active subscription plan.
        </p>
      </article>

      <article class="feature">
        <h3>2. Before Cancelling Your Membership</h3>

        <p>
          Before proceeding with cancellation, Gym4U encourages members to carefully consider the progress, consistency, and health benefits achieved throughout their fitness journey.
        </p>

        <p>
          Regular exercise contributes significantly to physical well-being, mental health, stress reduction, energy levels, and long-term lifestyle improvement. Many members who considered cancelling later found that adjusting schedules or reducing activity frequency allowed them to maintain healthy habits without fully ending their memberships.
        </p>

        <p>
          Gym4U continuously works to provide flexible schedules, multiple training options, modern facilities, and supportive trainers in order to help members remain motivated and engaged.
        </p>
      </article>

      <article class="feature">
        <h3>3. Subscription and Billing Information</h3>

        <p>
          Membership cancellations prevent future automatic renewals after the current subscription period ends. Charges already processed for the active billing cycle are generally non-refundable unless otherwise required by applicable law.
        </p>

        <p>
          Members are responsible for ensuring cancellation requests are submitted before renewal dates. Failure to cancel before the next billing cycle may result in automatic renewal according to the active membership plan.
        </p>

        <p>
          Refund requests related to subscription payments are evaluated on a case-by-case basis and may depend on the type of membership, payment method, and applicable legal obligations.
        </p>
      </article>

      <article class="feature">
        <h3>4. Effect on Existing Bookings</h3>

        <p>
          Once a membership reaches the end of its active period and becomes inactive, access to member-exclusive features and services may be restricted.
        </p>

        <p>
          Future class reservations, training sessions, appointments, and other scheduled activities associated with the cancelled membership may be automatically removed or cancelled by the platform.
        </p>

        <p>
          Members are encouraged to review upcoming bookings before submitting cancellation requests to avoid unexpected interruptions to scheduled activities.
        </p>
      </article>

      <article class="feature">
        <h3>5. Reactivating Memberships</h3>

        <p>
          Former members may reactivate their Gym4U subscriptions at any time, subject to current pricing, plan availability, and membership conditions.
        </p>

        <p>
          Gym4U welcomes returning members and continuously aims to provide improved facilities, updated equipment, new classes, and enhanced fitness experiences for all users.
        </p>
      </article>

      <div class="cancel-action">
        <a href="cancelations.php?cancel-membership=1" class="btn-primary">Continue to Cancellation</a>
      </div>

      <?php if (isset($_GET['cancel-membership'])): ?>
        <div class="modal-overlay" style="display:flex;">
          <div class="modal-box">
            <a href="cancelations.php" class="modal-close">×</a>

            <?php if (!isset($_SESSION['user_id'])): ?>

              <h2>Login Required</h2>

              <p>Please login before cancelling a membership.</p>

              <a href="login.php" class="btn-primary">Login</a>

            <?php elseif ($user['role'] !== 'member'): ?>

              <h2>Members Only</h2>

              <p>Only members can cancel memberships.</p>

            <?php elseif ($user['subscription_plan'] === 'none'): ?>

              <h2>No Active Membership</h2>

              <p>You do not currently have an active membership.</p>

            <?php else: ?>

              <h2>Cancel Membership</h2>

              <form method="post" action="actions/cancel_membership_action.php">
                <?php if ($errorMessage): ?>
                  <p class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></p>
                <?php endif; ?>
                <div class="form-grid">
                  <label>Name</label>
                  <input type="text" value="<?= htmlspecialchars($user['name']) ?>" readonly>

                  <label>Email</label>
                  <input type="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>

                  <label>Password</label>
                  <input type="password" name="password" placeholder="Confirm your password" required>
                </div>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

                <button type="submit" class="btn btn-cancel">Cancel Membership</button>
              </form>

            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancelations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank">Complaints Book</a>
    </p>

    <p>
      Membership Conditions · Regulations · Ethics Channel · Data Processing
    </p>

    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>