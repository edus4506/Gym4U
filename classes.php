<?php
  require_once __DIR__ . '/controllers/classesController.php';

  $successMessages = [
    'enrolled' => 'Enrollment confirmed.',
    'cancelled' => 'Enrollment cancelled.',
    'reviewed' => 'Review submitted.',
  ];
  $errorMessages = [
    'full' => 'That class is already full.',
    'already-enrolled' => 'You are already enrolled in that class.',
    'members-only' => 'Only members can enroll in classes or leave reviews.',
    'not-enrolled' => 'You can only review classes you are enrolled in.',
    'review' => 'Please choose a class, rating, and comment.',
    'invalid' => 'That class action could not be completed.',
  ];
  $successMessage = $successMessages[$_GET['success'] ?? ''] ?? null;
  $errorMessage = $errorMessages[$_GET['error'] ?? ''] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Classes | Gym4U</title>
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/classes.css">
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
        <li><a href="classes.php" aria-current="page">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="actions/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main class="main">
    <?php if ($successMessage): ?>
      <p class="alert alert-success"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <p class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <section class="section intro-panel intro-panel-dark">
      <h2>Classes at Our Gym</h2>
      <p>
        Explore every class division available at Gym4U. Each class includes
        the trainer who leads it, so you can choose the style and coach that fits your goals.
      </p>
    </section>

    <section class="section">
      <h2>Class Divisions</h2>
      <div class="division-grid">
        <?php foreach ($divisions as $division => $divClasses): ?>
          <?php $img = $divisionImages[$division] ?? null; ?>
          <article class="division-card">
            <?php if ($img): ?>
              <img class="division-image" src="<?= htmlspecialchars($img['src']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>">
            <?php endif; ?>
            <div class="division-content">
              <h3><?= htmlspecialchars($division) ?></h3>
              <p><?= htmlspecialchars($divClasses[0]['description']) ?></p>
              <p class="p division-meta">
                <strong>Classes:</strong> <?= htmlspecialchars(implode(', ', array_column($divClasses, 'name'))) ?>
              </p>
              <p class="p division-meta">
                <strong>Trainer:</strong> <?= htmlspecialchars($divClasses[0]['trainer_name']) ?>
              </p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section schedule-box">
      <h2>Weekly Class Schedule</h2>
      <table class="table">
        <caption class="caption">All classes and trainers at Gym4U</caption>
        <thead class="thead">
          <tr class="tr">
            <th class="th" scope="col">Class</th>
            <th class="th" scope="col">Trainer</th>
            <th class="th" scope="col">Day</th>
            <th class="th" scope="col">Time</th>
          </tr>
        </thead>
        <tbody class="tbody">
          <?php foreach ($schedule as $slot): ?>
            <tr class="tr">
              <th class="th" scope="row"><?= htmlspecialchars($slot['class_name']) ?></th>
              <td class="td"><?= htmlspecialchars($slot['trainer_name']) ?></td>
              <td class="td"><?= htmlspecialchars($slot['day']) ?></td>
              <td class="td"><?= htmlspecialchars($slot['time']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="section class-filters">
      <h2>Browse Classes &amp; Enroll</h2>
      <form class="filter-group" method="get" action="classes.php">
        <select name="type" aria-label="Filter by class type">
          <option value="">All Types</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= (int)$category['id'] ?>" <?= $filters['type'] === (string)$category['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select name="trainer" aria-label="Filter by trainer">
          <option value="">All Trainers</option>
          <?php foreach ($trainersForFilter as $t): ?>
            <option value="<?= (int)$t['id'] ?>" <?= $filters['trainer'] === (string)$t['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($t['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select name="day" aria-label="Filter by day">
          <option value="">All Days</option>
          <?php foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day): ?>
            <option value="<?= htmlspecialchars($day) ?>" <?= strtolower($filters['day']) === $day ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucfirst($day)) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select id="time" name="time" aria-label="Filter by time">
          <option value="">Any time</option>
          <optgroup label="Morning">
            <?php foreach (['06:00', '07:00', '08:00', '09:00', '10:00', '11:00'] as $time): ?>
              <option value="<?= htmlspecialchars($time) ?>" <?= $filters['time'] === $time ? 'selected' : '' ?>><?= htmlspecialchars($time) ?></option>
            <?php endforeach; ?>
          </optgroup>
          <optgroup label="Afternoon">
            <?php foreach (['12:00', '13:00', '14:00', '15:00', '16:00', '17:00'] as $time): ?>
              <option value="<?= htmlspecialchars($time) ?>" <?= $filters['time'] === $time ? 'selected' : '' ?>><?= htmlspecialchars($time) ?></option>
            <?php endforeach; ?>
          </optgroup>
          <optgroup label="Evening">
            <?php foreach (['18:00', '19:00', '20:00', '21:00'] as $time): ?>
              <option value="<?= htmlspecialchars($time) ?>" <?= $filters['time'] === $time ? 'selected' : '' ?>><?= htmlspecialchars($time) ?></option>
            <?php endforeach; ?>
          </optgroup>
        </select>
        <button type="submit" class="btn-filter">Apply Filters</button>
      </form>
    </section>

    <section class="section schedule-box">
      <h2>Class Booking &amp; Reviews</h2>
      <table class="table">
        <caption class="caption">Book your spot or leave a review for past sessions</caption>
        <thead class="thead">
          <tr class="tr">
            <th class="th">Class &amp; Rating</th>
            <th class="th">Trainer</th>
            <th class="th">Day &amp; Time</th>
            <th class="th">Capacity</th>
            <th class="th">Action</th>
          </tr>
        </thead>
        <tbody class="tbody">
          <?php foreach ($schedule as $slot): ?>
            <?php $full = $slot['enrolled'] >= $slot['capacity']; ?>
            <?php $enrolled = isset($memberEnrollments[$slot['id']]); ?>
            <tr class="tr <?= $enrolled ? 'enrolled-row' : '' ?>">
              <th class="th" scope="row"><?= htmlspecialchars($slot['class_name']) ?></th>
              <td class="td">
                <a href="trainers.php#<?= htmlspecialchars(strtolower(str_replace(' ', '-', $slot['trainer_name']))) ?>">
                  <?= htmlspecialchars($slot['trainer_name']) ?>
                </a>
              </td>
              <td class="td"><?= htmlspecialchars($slot['day']) ?>, <?= htmlspecialchars($slot['time']) ?></td>
              <td class="td"><?= (int)$slot['enrolled'] ?>/<?= (int)$slot['capacity'] ?></td>
              <td class="td">
                <?php if (!isset($_SESSION['user_id'])): ?>
                  <a class="btn btn-enroll" href="login.php">Login</a>
                <?php elseif (($_SESSION['user_role'] ?? '') !== 'member'): ?>
                  <span class="muted">Members only</span>
                <?php elseif ($enrolled): ?>
                  <form method="post" action="actions/class_enrollment_action.php">
                    <input type="hidden" name="schedule_id" value="<?= (int)$slot['id'] ?>">
                    <input type="hidden" name="mode" value="cancel">
                    <button class="btn btn-cancel" type="submit">Cancel</button>
                  </form>
                <?php elseif ($full): ?>
                  <button class="btn" disabled>Full</button>
                <?php else: ?>
                  <form method="post" action="actions/class_enrollment_action.php">
                    <input type="hidden" name="schedule_id" value="<?= (int)$slot['id'] ?>">
                    <input type="hidden" name="mode" value="enroll">
                    <button class="btn btn-enroll" type="submit">Enroll</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="section schedule-box">
      <h2>Main Training Area Equipment</h2>
      <table class="table">
        <caption class="caption">Current equipment availability</caption>
        <thead class="thead">
          <tr class="tr">
            <th class="th" scope="col">Equipment</th>
            <th class="th" scope="col">Quantity</th>
            <th class="th" scope="col">Status</th>
          </tr>
        </thead>
        <tbody class="tbody">
          <?php foreach ($equipment as $item): ?>
            <tr class="tr">
              <th class="th" scope="row"><?= htmlspecialchars($item['name']) ?></th>
              <td class="td"><?= (int)$item['quantity'] ?></td>
              <td class="td"><?= htmlspecialchars(ucfirst($item['status'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section class="section cta-section">
      <h2>Want to Know More About Our Trainers?</h2>
      <p>Check full trainer profiles, specializations, and experience details.</p>
      <p><a href="trainers.php">See more about trainers</a></p>
    </section>

    <section class="section review-form-container">
      <h2>Leave a Review</h2>
      <form class="review-form" method="post" action="actions/review_action.php">
        <label for="schedule_id">Class:</label>
        <select id="schedule_id" name="schedule_id" required>
          <option value="">Choose an enrolled class</option>
          <?php foreach ($schedule as $slot): ?>
            <?php if (isset($memberEnrollments[$slot['id']])): ?>
              <option value="<?= (int)$slot['id'] ?>">
                <?= htmlspecialchars($slot['class_name'] . ' - ' . $slot['day'] . ' ' . $slot['time']) ?>
              </option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
        <div class="rating-row">
          <label>Rating:</label>
          <div class="star-rating" aria-label="Rating">
              <span onclick="gfg(1)" class="star">★</span>
              <span onclick="gfg(2)" class="star">★</span>
              <span onclick="gfg(3)" class="star">★</span>
              <span onclick="gfg(4)" class="star">★</span>
              <span onclick="gfg(5)" class="star">★</span>
          </div>
        </div>
        <textarea name="comment" placeholder="Share your experience..." rows="3" required></textarea>
        <button type="submit" class="btn btn-enroll">Submit Review</button>
      </form>
    </section>

    <section class="section">
      <h2>Member Feedback</h2>
      <div class="division-grid">
        <?php foreach ($reviews as $review): ?>
          <article class="review-card">
            <h3><?= htmlspecialchars($review['class_name']) ?></h3>
            <div class="review-header">
              <strong><?= htmlspecialchars($review['name']) ?></strong>
              <span><?= str_repeat('★', (int)$review['rating']) ?></span>
            </div>
            <p class="r-p"><?= htmlspecialchars($review['comment']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
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
  <script src="js/main.js"></script>
  <script src="js/review.js"></script>
</body>
</html>
