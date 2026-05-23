<?php
  require_once __DIR__ . '/controllers/profileController.php';
  require_once __DIR__ . '/utils/csrf.php';

  $photo = $user['profile_photo'] ?: 'images/gym4u_icon.png';
  $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  $statuses = ['available', 'maintenance', 'unavailable'];
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Profile | Gym4U</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/profile.css">
</head>
<body>
  <header>
    <div>
      <a href="index.php">
        <img src="images/gym4u_logo.png" alt="Gym4U logo" class="logo">
      </a>
    </div>
    <div class="middle"><h1>Gym4U</h1></div>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle" class="hamburger">☰</label>
    <nav class="right" aria-label="Main navigation">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="classes.php">Classes</a></li>
        <li><a href="trainers.php">Trainers</a></li>
        <li><a href="equipment.php">Equipment</a></li>
        <li><a href="profile.php" aria-current="page">Profile</a></li>
        <li><a href="actions/logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main class="profile-page">
    <section class="profile-header">
      <img class="profile-photo" src="<?= htmlspecialchars($photo) ?>" alt="Profile photo">
      <div>
        <p class="profile-eyebrow"><?= htmlspecialchars($user['role']) ?></p>
        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <p>@<?= htmlspecialchars($user['username']) ?></p>
      </div>
      <?php if ($user['role'] === 'member'): ?>
        <?php if ($user['subscription_plan'] && $user['subscription_plan'] !== 'none'): ?>
          <img class="subscription-badge" src="images/badges/<?= htmlspecialchars($user['subscription_plan']) ?>.png" alt="Subscription badge">
        <?php else: ?>
          <p>Your membership is currently inactive. <a href="subscriptions.php">Reactivate now</a>.</p>
        <?php endif; ?>
      <?php elseif ($user['role'] === 'trainer'): ?>
        <img class="subscription-badge" src="images/badges/trainer.png" alt="Trainer badge">
      <?php elseif ($user['role'] === 'admin'): ?>
        <img class="subscription-badge" src="images/badges/admin.png" alt="Admin badge">
      <?php endif; ?>
    </section>

    <?php if ($successMessage): ?>
      <p class="alert alert-success"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <p class="alert alert-error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <section class="profile-panel">
      <h2>Edit Profile</h2>
      <form class="profile-form" method="post" action="actions/profile_action.php" enctype="multipart/form-data">
        <div class="form-grid">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>

          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
          </div>

          <div class="form-group">
            <label for="profile_photo">Profile photo</label>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/gif,image/webp">
          </div>
        </div>

        <div class="password-panel">
          <h3>Change Password</h3>
          <div class="form-grid">
            <div class="form-group">
              <label for="current_password">Current password</label>
              <input type="password" id="current_password" name="current_password" autocomplete="current-password">
            </div>

            <div class="form-group">
              <label for="new_password">New password</label>
              <input type="password" id="new_password" name="new_password" autocomplete="new-password">
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm new password</label>
              <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password">
            </div>
          </div>
        </div>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <button type="submit" class="btn-primary">Save Changes</button>
      </form>
    </section>

    <?php if ($user['role'] === 'member'): ?>
      <section class="profile-panel">
        <h2>My Enrolled Classes</h2>
        <?php if (!$memberEnrollments): ?>
          <p>You are not enrolled in any classes yet. Browse the <a href="classes.php">class schedule</a> to book a spot.</p>
        <?php else: ?>
          <table class="table">
            <thead>
              <tr>
                <th class="th">Class</th>
                <th class="th">Trainer</th>
                <th class="th">When</th>
                <th class="th">Capacity</th>
                <th class="th">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($memberEnrollments as $slot): ?>
                <tr>
                  <th class="th" scope="row"><?= htmlspecialchars($slot['class_name']) ?></th>
                  <td class="td"><?= htmlspecialchars($slot['trainer_name']) ?></td>
                  <td class="td"><?= htmlspecialchars($slot['day']) ?>, <?= htmlspecialchars($slot['time']) ?></td>
                  <td class="td"><?= (int)$slot['enrolled'] ?>/<?= (int)$slot['capacity'] ?></td>
                  <td class="td">
                    <form method="post" action="actions/class_enrollment_action.php">
                      <input type="hidden" name="schedule_id" value="<?= (int)$slot['schedule_id'] ?>">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                      <input type="hidden" name="mode" value="cancel">
                      <button class="btn btn-cancel" type="submit">Cancel</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if ($user['role'] === 'trainer' && $trainerProfile): ?>
      <section class="profile-panel">
        <h2>Trainer Public Profile</h2>
        <form class="profile-form" method="post" action="actions/trainer_profile_action.php">
          <div class="form-grid">
            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" id="title" name="title" value="<?= htmlspecialchars($trainerProfile['title'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="experience_years">Experience years</label>
              <input type="number" id="experience_years" name="experience_years" min="0" value="<?= (int)($trainerProfile['experience_years'] ?? 0) ?>">
            </div>
            <div class="form-group">
              <label for="phone">Phone</label>
              <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($trainerProfile['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="certifications">Certifications</label>
              <input type="text" id="certifications" name="certifications" value="<?= htmlspecialchars($trainerProfile['certifications'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label for="specialization_ids">Specializations</label>
              <select id="specialization_ids" name="specialization_ids[]" multiple size="6" aria-label="Specializations">
                <?php foreach ($categories as $category): ?>
                  <option
                    value="<?= (int)$category['id'] ?>"
                    <?= in_array((int)$category['id'], $trainerCategoryIds, true) ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($category['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($trainerProfile['bio'] ?? '') ?></textarea>
          </div>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <button type="submit" class="btn-primary">Save Trainer Profile</button>
        </form>
      </section>

      <section class="profile-panel">
        <h2>Assigned Class Schedule</h2>
        <div class="admin-list">
          <?php foreach ($trainerSchedule as $slot): ?>
            <form class="inline-admin-form" method="post" action="actions/trainer_schedule_action.php">
              <input type="hidden" name="schedule_id" value="<?= (int)$slot['id'] ?>">
              <strong><?= htmlspecialchars($slot['class_name']) ?></strong>
              <select name="day" aria-label="Day">
                <?php foreach ($days as $day): ?>
                  <option value="<?= htmlspecialchars($day) ?>" <?= $slot['day'] === $day ? 'selected' : '' ?>><?= htmlspecialchars($day) ?></option>
                <?php endforeach; ?>
              </select>
              <input type="time" name="time" value="<?= htmlspecialchars($slot['time']) ?>" aria-label="Time">
              <input type="number" name="duration_minutes" min="15" value="<?= (int)$slot['duration_minutes'] ?>" aria-label="Duration minutes">
              <input type="number" name="capacity" min="1" value="<?= (int)$slot['capacity'] ?>" aria-label="Capacity">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <span class="muted"><?= (int)$slot['enrolled'] ?> enrolled</span>
              <button class="btn btn-enroll" type="submit">Save</button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="profile-panel">
        <h2>Class Rosters</h2>
        <?php if (!$trainerRoster): ?>
          <p>No members are enrolled in your classes yet.</p>
        <?php else: ?>
          <table class="table">
            <thead><tr><th class="th">Class</th><th class="th">When</th><th class="th">Member</th><th class="th">Email</th></tr></thead>
            <tbody>
              <?php foreach ($trainerRoster as $entry): ?>
                <tr>
                  <th class="th" scope="row"><?= htmlspecialchars($entry['class_name']) ?></th>
                  <td class="td"><?= htmlspecialchars($entry['day']) ?>, <?= htmlspecialchars($entry['time']) ?></td>
                  <td class="td"><?= htmlspecialchars($entry['name']) ?></td>
                  <td class="td"><?= htmlspecialchars($entry['email']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if ($user['role'] === 'admin'): ?>
      <section class="profile-panel">
        <div class="admin-header">
          <h2>Manage Members, Trainers, and Admins</h2>

          <a href="manage_users.php" class="btn-primary">Manage Users</a>
        </div>
      </section>

      <section class="profile-panel">
        <div class="admin-header">
          <h2>Manage Classes</h2>

          <a href="manage_classes.php" class="btn-primary">Manage Classes</a>
        </div>
      </section>

      <section class="profile-panel">
        <div class="admin-header">
          <h2>Manage Schedule</h2>

          <a href="manage_schedules.php" class="btn-primary">Manage Schedule</a>
        </div>
      </section>

      <section class="profile-panel">
        <div class="admin-header">
          <h2>Manage Equipment</h2>

          <a href="manage_equipment.php" class="btn-primary">Manage Equipment</a>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancelations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions Â· Regulations Â· Ethics Channel Â· Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>
