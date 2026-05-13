<?php
declare(strict_types = 1);
require_once __DIR__ . '/database/db.php';

$db = getDatabaseConnection();

$reviews = $db->query("
    SELECT r.rating, r.comment, u.name, c.name AS class_name
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN class_schedule cs ON r.schedule_id = cs.id
    JOIN classes c ON cs.class_id = c.id
    ORDER BY r.id DESC
    LIMIT 3
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Home | Gym4U</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/index.css">
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
        <li><a href="index.php" aria-current="page">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="#">Profile</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <!-- HERO -->
    <section class="section hero">
      <div class="slider">
        <img src="images/index/slider1.png" class="slide" alt="Gym training image 1">
        <img src="images/index/slider2.png" class="slide" alt="Gym training image 2">
      </div>
      <div class="hero-text">
        <h2>Transform Your Body</h2>
        <p>
          Join Gym4U and start your fitness journey with expert guidance,
          modern equipment, and motivating classes.
        </p>
        <a class="btn-primary">Get Started</a>
      </div>
    </section>

    <!-- ABOUT -->
    <section class="section about">
      <h2>Why Gym4U?</h2>
      <div class="features">
        <div class="feature">
          <h3>No Contracts</h3>
          <p>
            Enjoy full flexibility at Gym4U. Join and leave whenever you want -
            no minimum commitment, no fine print.
          </p>
        </div>
        <div class="feature">
          <h3>Spacious Facilities</h3>
          <p>
            We offer 7 different training zones so you can build your routine
            your way - everything you need to train on your terms.
          </p>
        </div>
        <div class="feature">
          <h3>Wide Variety of Classes</h3>
          <p>
            No room for boredom. Try our supervised classes like Body Pump,
            Yoga, Zumba, Hyrox, Cycling and more - you'll want to try them all.
          </p>
        </div>
        <div class="feature">
          <h3>Extended Opening Hours</h3>
          <p>
            Train on your schedule. Gym4U offers flexible hours so you can work out whenever it suits you.
          </p>
        </div>
      </div>
    </section>

    <!-- FEATURE CARDS -->
    <section class="features-modern">
      <h2 class="visually-hidden">Gym Features Overview</h2>
      <div class="grid-2x2">
        <article class="card">
          <img src="images/index/trainers.png" alt="Trainers">
          <div class="overlay"></div>
          <div class="content">
            <h3>Trainers</h3>
            <p>Train with experienced professionals!</p>
            <a href="trainers.php">View Trainers</a>
          </div>
        </article>
        <article class="card">
          <img src="images/index/classes.png" alt="Classes">
          <div class="overlay"></div>
          <div class="content">
            <h3>Gym Classes</h3>
            <p>Join any classes: Yoga, HIIT and much more!</p>
            <a href="classes.php">View Classes</a>
          </div>
        </article>
        <article class="card">
          <img src="images/index/equipment.png" alt="Equipment">
          <div class="overlay"></div>
          <div class="content">
            <h3>Modern Equipment</h3>
            <p>High-quality equipment for efficient workouts.</p>
          </div>
        </article>
        <article class="card light">
          <img src="images/index/drinks.png" alt="Drinks">
          <div class="overlay light"></div>
          <div class="content">
            <h3>Healthy Drinks</h3>
            <p>Stay hydrated with water, juices, and energy drinks.</p>
          </div>
        </article>
      </div>
    </section>

    <!-- CTA -->
    <section class="section cta-section" id="cta-section">
      <h2 class="h2">Your fitness journey starts here!</h2>
      <p class="p">
        No commitments, no pressure. Try Gym4U for free and explore our classes,
        expert trainers, and modern facilities. Check the schedule and find what suits you.
      </p>
      <p class="p"><a class="a" href="classes.php">See more about classes!</a></p>
    </section>

    <!-- REVIEW FORM -->
    <section class="section review-form-container">
      <h2>Your Feedback matters!</h2>
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
        <button type="submit" class="btn-primary">Submit Review</button>
      </form>
    </section>

    <!-- DYNAMIC REVIEWS -->
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
            <p class="review-text"><?= htmlspecialchars($review['comment']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>
      <a href="#">Terms of Use</a> |
      <a href="#">Cancellations</a> |
      <a href="#">Complaints Book</a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>