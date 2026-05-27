<?php require_once __DIR__ . '/controllers/trainersController.php'; ?>

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
    <div class="middle">
      <h1>Gym4U</h1>
    </div>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="hamburger">☰</label>
    <nav class="right" aria-label="Main navigation">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php" aria-current="page">Trainers</a></li>
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

  <main class="trainers-page">
    <section class="section intro-panel intro-panel-dark">
      <h2>Meet Our Trainers</h2>
      <p>
        Our coaching team combines experience, motivation, and technical
        knowledge to help every member train with confidence. Discover each
        trainer's background, specializations, and classes.
      </p>
    </section>

    <!-- FILTERS -->
    <section class="section filter-panel">
      <h2>Find the Right Trainer</h2>
      <form class="filter-form filter-form-4" action="trainers.php" method="get">
        <div class="form-group">
          <label for="trainer-search">Search by name</label>
          <input type="text" id="trainer-search" name="trainer-search" placeholder="e.g. Eduardo Amaral" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="form-group">
          <label for="specialization">Specialization</label>
          <select id="specialization" name="specialization">
            <option value="">All specializations</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= (int)$cat['id'] ?>" <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="experience">Experience level</label>
          <select id="experience" name="experience">
            <option value="">Any experience</option>
            <option value="1-3" <?= $experience === '1-3' ? 'selected' : '' ?>>1-3 years</option>
            <option value="4-6" <?= $experience === '4-6' ? 'selected' : '' ?>>4-6 years</option>
            <option value="7-plus" <?= $experience === '7-plus' ? 'selected' : '' ?>>7+ years</option>
          </select>
        </div>
        <div class="form-group">
          <label for="class-day">Available on</label>
          <select id="class-day" name="class-day">
            <option value="">Any day</option>
            <option value="monday" <?= $classDay === 'monday' ? 'selected' : '' ?>>Monday</option>
            <option value="tuesday" <?= $classDay === 'tuesday' ? 'selected' : '' ?>>Tuesday</option>
            <option value="wednesday" <?= $classDay === 'wednesday' ? 'selected' : '' ?>>Wednesday</option>
            <option value="thursday" <?= $classDay === 'thursday' ? 'selected' : '' ?>>Thursday</option>
            <option value="friday" <?= $classDay === 'friday' ? 'selected' : '' ?>>Friday</option>
            <option value="saturday" <?= $classDay === 'saturday' ? 'selected' : '' ?>>Saturday</option>
          </select>
        </div>
        <button type="submit">Apply Filters</button>
      </form>
    </section>

    <!-- TRAINER CARDS -->
    <section>
      <h2>Our Coaching Staff</h2>
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
              <h3><?= htmlspecialchars($trainer['name']) ?></h3>
              <p class="trainer-role"><?= htmlspecialchars($trainer['title']) ?></p>
              <p><?= htmlspecialchars($trainer['bio']) ?></p>
              <p class="trainer-meta"><strong>Experience:</strong> <?= (int)$trainer['experience_years'] ?> years</p>
              <p class="trainer-meta"><strong>Certifications:</strong> <?= htmlspecialchars($trainer['certifications']) ?></p>
              <p class="trainer-meta"><strong>Specializations:</strong> <?= htmlspecialchars($trainer['specializations']) ?></p>
              <p class="trainer-meta"><strong>Classes:</strong> <?= htmlspecialchars($classesPerTrainer[$trainer['id']] ?? 'N/A') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- OVERVIEW TABLE -->
    <section class="section schedule-box clean-panel">
      <h2>Trainer Overview</h2>
      <table>
        <caption>Summary of trainer specializations and experience</caption>
        <thead>
          <tr>
            <th scope="col">Trainer</th>
            <th scope="col">Specialization</th>
            <th scope="col">Experience</th>
            <th scope="col">Main Classes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($trainers as $trainer): ?>
            <tr>
              <th scope="row"><?= htmlspecialchars($trainer['name']) ?></th>
              <td><?= htmlspecialchars($trainer['specializations']) ?></td>
              <td><?= (int)$trainer['experience_years'] ?> years</td>
              <td><?= htmlspecialchars($classesPerTrainer[$trainer['id']] ?? 'N/A') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <!-- TRAINING FOCUS -->
    <section class="section training-focus">
      <h2>What our trainers can help you with</h2>
      <div class="focus-grid">
        <article class="focus-card">
          <h3>Build Strength</h3>
          <p>Improve muscle strength, endurance, and overall physical performance with structured and progressive training.</p>
        </article>
        <article class="focus-card">
          <h3>Improve Mobility</h3>
          <p>Work on flexibility, posture, joint movement, and recovery through mobility and technique-focused sessions.</p>
        </article>
        <article class="focus-card">
          <h3>Boost Cardio Fitness</h3>
          <p>Increase stamina and heart health with cycling, interval training, and high-energy cardio sessions.</p>
        </article>
        <article class="focus-card">
          <h3>Develop Core Control</h3>
          <p>Strengthen your core, improve balance, and support better posture with pilates and functional training.</p>
        </article>
        <article class="focus-card">
          <h3>Train with Confidence</h3>
          <p>Whether you are a beginner or more advanced, our trainers help you progress safely and consistently.</p>
        </article>
        <article class="focus-card">
          <h3>Reach Personal Goals</h3>
          <p>Get support for fat loss, muscle gain, conditioning, or general fitness through personalized guidance.</p>
        </article>
      </div>
    </section>

    <!-- FAQ -->
    <section class="section trainers-faq">
      <h2>FAQ</h2>
      <details class="faq-item">
        <summary>How can I choose the right trainer for my goals?</summary>
        <p>You can compare each trainer's specializations, experience, certifications, and main classes to find the best fit for your goals.</p>
      </details>
      <details class="faq-item">
        <summary>Can I contact a trainer directly?</summary>
        <p>Yes. You can find each trainer's phone number and email address in the contact section of this page.</p>
      </details>
      <details class="faq-item">
        <summary>Do trainers only teach group classes?</summary>
        <p>No. Some trainers also provide personal training sessions depending on their specialization and availability.</p>
      </details>
      <details class="faq-item">
        <summary>Are all trainers certified?</summary>
        <p>Yes. Every trainer profile includes certification and experience information.</p>
      </details>
      <details class="faq-item">
        <summary>Where can I see the classes taught by each trainer?</summary>
        <p>You can check the classes listed in each profile and also explore the full class schedule on the classes page.</p>
      </details>
    </section>

    <!-- CONTACT -->
    <section class="section trainers-contact">
      <h2>Get in touch with our trainers</h2>
      <div class="contact-grid">
        <?php foreach ($trainers as $trainer): ?>
          <article class="contact-card">
            <h3><?= htmlspecialchars($trainer['name']) ?></h3>
            <p><strong>Phone:</strong> <?= htmlspecialchars($trainer['phone']) ?></p>
            <p><strong>Email:</strong>
              <a href="mailto:<?= htmlspecialchars($trainer['email']) ?>">
                <?= htmlspecialchars($trainer['email']) ?>
              </a>
            </p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section cta-section">
      <h2>Ready to train with us?</h2>
      <p>Explore our class <a href="classes.php">schedule</a> and find the right trainer for your goals.</p>
    </section>
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
  <script src="js/trainers.js"></script>
</body>
</html>
