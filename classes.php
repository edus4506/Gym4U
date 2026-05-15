<?php
  require_once __DIR__ . '/controllers/classesController.php';
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
        <li><a href="#">Profile</a></li>
      </ul>
    </nav>
  </header>

  <main class="main">
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
      <form class="filter-group">
        <select name="type" aria-label="Filter by class type">
          <option value="">All Types</option>
          <option value="yoga">Yoga &amp; Mobility</option>
          <option value="cardio">Cycling &amp; Cardio</option>
          <option value="hiit">HIIT &amp; Conditioning</option>
          <option value="functional">Functional Training</option>
          <option value="pilates">Pilates &amp; Core</option>
          <option value="personal">Personal Training</option>
          <option value="toning">Body Toning</option>
        </select>
        <select name="trainer" aria-label="Filter by trainer">
          <option value="">All Trainers</option>
          <?php foreach ($db->query("SELECT t.id AS id, u.name AS name FROM trainers t JOIN users u ON t.user_id = u.id ORDER BY u.name")->fetchAll() as $t): ?>
            <option value="<?= htmlspecialchars(strtolower(explode(' ', $t['name'])[0])) ?>">
              <?= htmlspecialchars($t['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <select name="day" aria-label="Filter by day">
          <option value="">All Days</option>
          <option value="monday">Monday</option>
          <option value="tuesday">Tuesday</option>
          <option value="wednesday">Wednesday</option>
          <option value="thursday">Thursday</option>
          <option value="friday">Friday</option>
          <option value="saturday">Saturday</option>
        </select>
        <select id="time" name="time" aria-label="Filter by time">
          <option value="Any time">Any time</option>
          <optgroup label="Morning">
            <option value="06:00">06:00</option><option value="07:00">07:00</option>
            <option value="08:00">08:00</option><option value="09:00">09:00</option>
            <option value="10:00">10:00</option><option value="11:00">11:00</option>
          </optgroup>
          <optgroup label="Afternoon">
            <option value="12:00">12:00</option><option value="13:00">13:00</option>
            <option value="14:00">14:00</option><option value="15:00">15:00</option>
            <option value="16:00">16:00</option><option value="17:00">17:00</option>
          </optgroup>
          <optgroup label="Evening">
            <option value="18:00">18:00</option><option value="19:00">19:00</option>
            <option value="20:00">20:00</option><option value="21:00">21:00</option>
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
            <tr class="tr">
              <th class="th" scope="row"><?= htmlspecialchars($slot['class_name']) ?></th>
              <td class="td">
                <a href="trainers.php#<?= htmlspecialchars(strtolower(str_replace(' ', '-', $slot['trainer_name']))) ?>">
                  <?= htmlspecialchars($slot['trainer_name']) ?>
                </a>
              </td>
              <td class="td"><?= htmlspecialchars($slot['day']) ?>, <?= htmlspecialchars($slot['time']) ?></td>
              <td class="td"><?= (int)$slot['enrolled'] ?>/<?= (int)$slot['capacity'] ?></td>
              <td class="td">
                <?php if ($full): ?>
                  <button class="btn" disabled>Full</button>
                <?php else: ?>
                  <button class="btn btn-enroll">Enroll</button>
                <?php endif; ?>
              </td>
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
      <form class="review-form" method="post" action="submit_review.php">
        <label for="rating">Rating:</label>
        <select id="rating" name="rating">
          <option value="5">5 Stars - Excellent</option>
          <option value="4">4 Stars - Very Good</option>
          <option value="3">3 Stars - Average</option>
          <option value="2">2 Stars - Poor</option>
          <option value="1">1 Star - Terrible</option>
        </select>
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
</body>
</html>