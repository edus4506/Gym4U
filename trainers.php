<?php
declare(strict_types = 1);
require_once __DIR__ . '/database/db.php';

$db = getDatabaseConnection();

$trainers = $db->query("SELECT t.id, t.bio, t.experience_years, t.certifications, t.phone, t.title, u.name, u.email, GROUP_CONCAT(cat.name, ', ') AS specializations
  FROM trainers t 
  JOIN users u ON u.id = t.user_id 
  LEFT JOIN trainer_categories tc ON tc.trainer_id = t.id
  LEFT JOIN categories cat ON cat.id = tc.category_id
  GROUP BY t.id
  ORDER BY u.name")->fetchAll();

$classesPerTrainer = [];
foreach ($db->query("
    SELECT c.trainer_id, GROUP_CONCAT(c.name, ', ') AS class_list
    FROM classes c
    GROUP BY c.trainer_id
")->fetchAll() as $row) {
    $classesPerTrainer[$row['trainer_id']] = $row['class_list'];
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trainers | Gym4U</title>
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/trainers.css">
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
        <li><a href="trainers.php" aria-current="page">Trainers</a></li>
        <li><a href="#">Profile</a></li>
      </ul>
    </nav>
  </header>

  <main class="trainers-page">
    <section class="section intro-panel intro-panel-dark">
      <h2 class="h2">Meet Our Trainers</h2>
      <p class="p">
        Our coaching team combines experience, motivation, and technical
        knowledge to help every member train with confidence. Discover each
        trainer's background, specializations, and classes.
      </p>
    </section>

    <!-- FILTERS -->
    <section class="section filter-panel">
      <h2 class="h2">Find the Right Trainer</h2>
      <form class="filter-form filter-form-4" action="#" method="get">
        <div class="form-group">
          <label for="trainer-search">Search by name</label>
          <input type="text" id="trainer-search" name="trainer-search" placeholder="e.g. Eduardo Amaral">
        </div>
        <div class="form-group">
          <label for="specialization">Specialization</label>
          <select id="specialization" name="specialization">
            <option value="">All specializations</option>
            <option value="yoga">Yoga</option>
            <option value="spinning">Spinning</option>
            <option value="pilates">Pilates</option>
            <option value="hiit">HIIT</option>
            <option value="personal-training">Personal Training</option>
          </select>
        </div>
        <div class="form-group">
          <label for="experience">Experience level</label>
          <select id="experience" name="experience">
            <option value="">Any experience</option>
            <option value="1-3">1-3 years</option>
            <option value="4-6">4-6 years</option>
            <option value="7-plus">7+ years</option>
          </select>
        </div>
        <div class="form-group">
          <label for="class-day">Available on</label>
          <select id="class-day" name="class-day">
            <option value="">Any day</option>
            <option value="monday">Monday</option>
            <option value="tuesday">Tuesday</option>
            <option value="wednesday">Wednesday</option>
            <option value="thursday">Thursday</option>
            <option value="friday">Friday</option>
            <option value="saturday">Saturday</option>
          </select>
        </div>
        <button type="submit">Apply Filters</button>
      </form>
    </section>

    <!-- TRAINER CARDS -->
    <section class="section">
      <h2 class="h2">Our Coaching Staff</h2>
      <div class="trainer-grid">
        <?php foreach ($trainers as $trainer): ?>
          <?php $slug = strtolower(str_replace(' ', '-', $trainer['name'])); ?>
          <article class="trainer-card" id="<?= htmlspecialchars($slug) ?>">
            <img
              class="trainer-image"
              src="images/trainers/<?= htmlspecialchars($slug) ?>.png"
              alt="Portrait of <?= htmlspecialchars($trainer['name']) ?>"
              onerror="this.style.display='none'"
            >
            <div class="trainer-content">
              <h3 class="h3"><?= htmlspecialchars($trainer['name']) ?></h3>
              <p class="p trainer-role"><?= htmlspecialchars($trainer['title']) ?></p>
              <p class="p"><?= htmlspecialchars($trainer['bio']) ?></p>
              <p class="p trainer-meta"><strong>Experience:</strong> <?= (int)$trainer['experience_years'] ?> years</p>
              <p class="p trainer-meta"><strong>Certifications:</strong> <?= htmlspecialchars($trainer['certifications']) ?></p>
              <p class="p trainer-meta"><strong>Specializations:</strong> <?= htmlspecialchars($trainer['specializations']) ?></p>
              <p class="p trainer-meta"><strong>Classes:</strong> <?= htmlspecialchars($classesPerTrainer[$trainer['id']] ?? 'N/A') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- OVERVIEW TABLE -->
    <section class="section schedule-box clean-panel">
      <h2 class="h2">Trainer Overview</h2>
      <table class="table">
        <caption class="caption">Summary of trainer specializations and experience</caption>
        <thead class="thead">
          <tr class="tr">
            <th class="th" scope="col">Trainer</th>
            <th class="th" scope="col">Specialization</th>
            <th class="th" scope="col">Experience</th>
            <th class="th" scope="col">Main Classes</th>
          </tr>
        </thead>
        <tbody class="tbody">
          <?php foreach ($trainers as $trainer): ?>
            <tr class="tr">
              <th class="th" scope="row"><?= htmlspecialchars($trainer['name']) ?></th>
              <td class="td"><?= htmlspecialchars($trainer['specializations']) ?></td>
              <td class="td"><?= (int)$trainer['experience_years'] ?> years</td>
              <td class="td"><?= htmlspecialchars($classesPerTrainer[$trainer['id']] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- TRAINING FOCUS -->
    <section class="section training-focus">
      <h2 class="h2">What our trainers can help you with</h2>
      <div class="focus-grid">
        <article class="focus-card">
          <h3 class="h3">Build Strength</h3>
          <p class="p">Improve muscle strength, endurance, and overall physical performance with structured and progressive training.</p>
        </article>
        <article class="focus-card">
          <h3 class="h3">Improve Mobility</h3>
          <p class="p">Work on flexibility, posture, joint movement, and recovery through mobility and technique-focused sessions.</p>
        </article>
        <article class="focus-card">
          <h3 class="h3">Boost Cardio Fitness</h3>
          <p class="p">Increase stamina and heart health with cycling, interval training, and high-energy cardio sessions.</p>
        </article>
        <article class="focus-card">
          <h3 class="h3">Develop Core Control</h3>
          <p class="p">Strengthen your core, improve balance, and support better posture with pilates and functional training.</p>
        </article>
        <article class="focus-card">
          <h3 class="h3">Train with Confidence</h3>
          <p class="p">Whether you are a beginner or more advanced, our trainers help you progress safely and consistently.</p>
        </article>
        <article class="focus-card">
          <h3 class="h3">Reach Personal Goals</h3>
          <p class="p">Get support for fat loss, muscle gain, conditioning, or general fitness through personalized guidance.</p>
        </article>
      </div>
    </section>

    <!-- FAQ -->
    <section class="section trainers-faq">
      <h2 class="h2">FAQ</h2>
      <details class="faq-item">
        <summary>How can I choose the right trainer for my goals?</summary>
        <p class="p">You can compare each trainer's specializations, experience, certifications, and main classes to find the best fit for your goals.</p>
      </details>
      <details class="faq-item">
        <summary>Can I contact a trainer directly?</summary>
        <p class="p">Yes. You can find each trainer's phone number and email address in the contact section of this page.</p>
      </details>
      <details class="faq-item">
        <summary>Do trainers only teach group classes?</summary>
        <p class="p">No. Some trainers also provide personal training sessions depending on their specialization and availability.</p>
      </details>
      <details class="faq-item">
        <summary>Are all trainers certified?</summary>
        <p class="p">Yes. Every trainer profile includes certification and experience information.</p>
      </details>
      <details class="faq-item">
        <summary>Where can I see the classes taught by each trainer?</summary>
        <p class="p">You can check the classes listed in each profile and also explore the full class schedule on the classes page.</p>
      </details>
    </section>

    <!-- CONTACT -->
    <section class="section trainers-contact">
      <h2 class="h2">Get in touch with our trainers</h2>
      <div class="contact-grid">
        <?php foreach ($trainers as $trainer): ?>
          <article class="contact-card">
            <h3 class="h3"><?= htmlspecialchars($trainer['name']) ?></h3>
            <p class="p"><strong>Phone:</strong> <?= htmlspecialchars($trainer['phone']) ?></p>
            <p class="p"><strong>Email:</strong>
              <a class="a" href="mailto:<?= htmlspecialchars($trainer['email']) ?>">
                <?= htmlspecialchars($trainer['email']) ?>
              </a>
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section cta-section">
      <h2 class="h2">Ready to train with us?</h2>
      <p class="p">Explore our class <a class="a" href="classes.php">schedule</a> and find the right trainer for your goals.</p>
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