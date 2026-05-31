<?php require_once __DIR__ . '/controllers/analyticsController.php'; ?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Analytics Dashboard | Gym4U</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/profile.css">
  <link rel="stylesheet" href="css/analytics.css">
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
            <button type="submit" class="btn-logout">Logout</button>
          </form>
        </li>
      </ul>
    </nav>
  </header>

  <main>

    <section class="profile-panel">
      <div class="admin-header">
        <h2>
          Analytics Dashboard
          <?php if ($trainerInfo): ?>
            <span class="analytics-trainer-name">— <?= htmlspecialchars($trainerInfo['name']) ?></span>
          <?php endif; ?>
        </h2>
        <a href="profile.php" class="btn-create">Back to Profile</a>
      </div>

      <?php if ($role === 'admin' && !empty($allTrainers)): ?>
        <form method="get" action="analytics.php" class="filter-group" style="margin-top: 1rem;">
          <select name="trainer_id" class="analytics-trainer-select">
            <?php foreach ($allTrainers as $t): ?>
              <option
                value="<?= (int)$t['id'] ?>"
                <?= (int)$t['id'] === $targetTrainerId ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($t['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      <?php endif; ?>
    </section>

    <section class="profile-panel">
      <h2>Overview</h2>
      <div class="analytics-cards">

        <div class="analytics-card">
          <span class="analytics-card-value" data-card="enrolled"><?= $totalEnrolled ?></span>
          <span class="analytics-card-label">Total Members Enrolled</span>
        </div>

        <div class="analytics-card">
          <span class="analytics-card-value" data-card="fillRate"><?= $fillRate ?>%</span>
          <span class="analytics-card-label">Overall Fill Rate</span>
        </div>

        <div class="analytics-card">
          <span class="analytics-card-value" data-card="rating">
            <?= $overallRating !== null ? $overallRating . ' ★' : '—' ?>
          </span>
          <span class="analytics-card-label">Average Rating</span>
        </div>

        <div class="analytics-card">
          <span class="analytics-card-value" data-card="reviews"><?= $totalReviews ?></span>
          <span class="analytics-card-label">Total Reviews</span>
        </div>

        <div class="analytics-card">
          <span class="analytics-card-value" data-card="slots"><?= count($classStats) ?></span>
          <span class="analytics-card-label">Active Schedule Slots</span>
        </div>

        <?php if ($topClass): ?>
          <div class="analytics-card analytics-card-highlight">
            <span class="analytics-card-value" data-card="topClass"><?= htmlspecialchars($topClass['class_name']) ?></span>
            <span class="analytics-card-label" data-card="topClassLabel">Most Popular Class (<?= $topClass['enrolled'] ?> enrolled)</span>
          </div>
        <?php endif; ?>

      </div>
    </section>

    <section class="profile-panel">
      <h2>Class Breakdown</h2>

      <?php if (empty($classStats)): ?>
        <p class="muted">No classes assigned yet.</p>
      <?php else: ?>
        <div class="admin-list analytics-breakdown">
          <?php foreach ($classStats as $slot): ?>
            <div class="analytics-row">
              <div class="analytics-row-main">
                <strong><?= htmlspecialchars($slot['class_name']) ?></strong>
                <span class="muted">
                  <?= htmlspecialchars($slot['day']) ?> at <?= htmlspecialchars($slot['time']) ?>
                </span>
              </div>

              <div class="analytics-row-stats">

                <div class="analytics-stat">
                  <span class="analytics-stat-label">Enrolled</span>
                  <span class="analytics-stat-value">
                    <?= $slot['enrolled'] ?> / <?= $slot['capacity'] ?>
                  </span>
                  <div class="analytics-bar-track">
                    <div
                      class="analytics-bar-fill <?= $slot['enrolled'] >= $slot['capacity'] ? 'full' : '' ?>"
                      style="width: <?= $slot['capacity'] > 0 ? min(100, round(($slot['enrolled'] / $slot['capacity']) * 100)) : 0 ?>%"
                    ></div>
                  </div>
                </div>

                <div class="analytics-stat">
                  <span class="analytics-stat-label">Avg Rating</span>
                  <span class="analytics-stat-value">
                    <?= $slot['avg_rating'] !== null ? $slot['avg_rating'] . ' ★' : '—' ?>
                  </span>
                </div>

                <div class="analytics-stat">
                  <span class="analytics-stat-label">Reviews</span>
                  <span class="analytics-stat-value"><?= $slot['review_count'] ?></span>
                </div>

              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <section class="profile-panel">
      <h2>Recent Reviews</h2>

      <div class="analytics-reviews">
        <?php if (empty($recentReviews)): ?>
          <p class="muted">No reviews yet.</p>
        <?php else: ?>
          <div class="division-grid">
            <?php foreach ($recentReviews as $review): ?>
              <article class="review-card">
                <h3><?= htmlspecialchars($review['class_name']) ?></h3>
                <div class="review-header">
                  <strong><?= htmlspecialchars($review['member_name']) ?></strong>
                  <span><?= str_repeat('★', (int)$review['rating']) ?></span>
                </div>
                <p class="r-p"><?= htmlspecialchars($review['comment']) ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

  </main>

  <footer>
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancellations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank">Complaints Book</a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
  <script src="js/analytics.js" defer></script>
</body>
</html>
