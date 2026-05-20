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
        <h2>Create User</h2>
        <form class="profile-form" method="post" action="actions/admin_user_action.php">
          <input type="hidden" name="mode" value="create">
          <div class="form-grid">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
              <option value="member">Member</option>
              <option value="trainer">Trainer</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <button class="btn-primary" type="submit">Create Account</button>
        </form>
      </section>

      <section class="profile-panel">
        <h2>Manage Members, Trainers, and Admins</h2>
        <div class="admin-list">
          <?php foreach ($adminUsers as $managedUser): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_user_action.php">
              <input type="hidden" name="mode" value="update">
              <input type="hidden" name="user_id" value="<?= (int)$managedUser['id'] ?>">
              <input type="text" name="name" value="<?= htmlspecialchars($managedUser['name']) ?>" aria-label="Name">
              <input type="text" name="username" value="<?= htmlspecialchars($managedUser['username']) ?>" aria-label="Username">
              <input type="email" name="email" value="<?= htmlspecialchars($managedUser['email']) ?>" aria-label="Email">
              <select name="role" aria-label="Role">
                <?php foreach (['member', 'trainer', 'admin'] as $role): ?>
                  <option value="<?= htmlspecialchars($role) ?>" <?= $managedUser['role'] === $role ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($role)) ?></option>
                <?php endforeach; ?>
              </select>
              <label class="checkbox-label"><input type="checkbox" name="active" <?= (int)$managedUser['active'] === 1 ? 'checked' : '' ?>> Active</label>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <button class="btn btn-enroll" type="submit">Save</button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="profile-panel">
        <h2>Class Catalog</h2>
        <form class="profile-form compact-form" method="post" action="actions/admin_class_action.php">
          <input type="hidden" name="mode" value="create">
          <div class="form-grid">
            <input type="text" name="name" placeholder="Class name" required>
            <input type="text" name="description" placeholder="Description">
            <select name="category_id" required>
              <?php foreach ($categories as $category): ?><option value="<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option><?php endforeach; ?>
            </select>
            <select name="trainer_id" required>
              <?php foreach ($trainers as $trainer): ?><option value="<?= (int)$trainer['id'] ?>"><?= htmlspecialchars($trainer['name']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <button class="btn-primary" type="submit">Create Class</button>
        </form>
        <div class="admin-list">
          <?php foreach ($adminClasses as $class): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_class_action.php">
              <input type="hidden" name="mode" value="update">
              <input type="hidden" name="class_id" value="<?= (int)$class['id'] ?>">
              <input type="text" name="name" value="<?= htmlspecialchars($class['name']) ?>" aria-label="Class name">
              <input type="text" name="description" value="<?= htmlspecialchars($class['description']) ?>" aria-label="Description">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <select name="category_id" aria-label="Category"><?php foreach ($categories as $category): ?><option value="<?= (int)$category['id'] ?>" <?= (int)$class['category_id'] === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option><?php endforeach; ?></select>
              <select name="trainer_id" aria-label="Trainer"><?php foreach ($trainers as $trainer): ?><option value="<?= (int)$trainer['id'] ?>" <?= (int)$class['trainer_id'] === (int)$trainer['id'] ? 'selected' : '' ?>><?= htmlspecialchars($trainer['name']) ?></option><?php endforeach; ?></select>
              <button class="btn btn-enroll" type="submit">Save</button>
              <button class="btn btn-cancel" type="submit" name="mode" value="delete">Remove</button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="profile-panel">
        <h2>Class Schedule</h2>
        <form class="profile-form compact-form" method="post" action="actions/admin_schedule_action.php">
          <input type="hidden" name="mode" value="create">
          <div class="form-grid">
            <select name="class_id" required><?php foreach ($adminClasses as $class): ?><option value="<?= (int)$class['id'] ?>"><?= htmlspecialchars($class['name']) ?></option><?php endforeach; ?></select>
            <select name="day"><?php foreach ($days as $day): ?><option value="<?= htmlspecialchars($day) ?>"><?= htmlspecialchars($day) ?></option><?php endforeach; ?></select>
            <input type="time" name="time" required>
            <input type="number" name="duration_minutes" min="15" value="45" placeholder="Duration">
            <input type="number" name="capacity" min="1" value="12" placeholder="Capacity">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          </div>
          <button class="btn-primary" type="submit">Add Schedule Slot</button>
        </form>
        <div class="admin-list">
          <?php foreach ($adminSchedules as $slot): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_schedule_action.php">
              <input type="hidden" name="mode" value="update">
              <input type="hidden" name="schedule_id" value="<?= (int)$slot['id'] ?>">
              <select name="class_id" aria-label="Class"><?php foreach ($adminClasses as $class): ?><option value="<?= (int)$class['id'] ?>" <?= (int)$slot['class_id'] === (int)$class['id'] ? 'selected' : '' ?>><?= htmlspecialchars($class['name']) ?></option><?php endforeach; ?></select>
              <select name="day" aria-label="Day"><?php foreach ($days as $day): ?><option value="<?= htmlspecialchars($day) ?>" <?= $slot['day'] === $day ? 'selected' : '' ?>><?= htmlspecialchars($day) ?></option><?php endforeach; ?></select>
              <input type="time" name="time" value="<?= htmlspecialchars($slot['time']) ?>" aria-label="Time">
              <input type="number" name="duration_minutes" min="15" value="<?= (int)$slot['duration_minutes'] ?>" aria-label="Duration">
              <input type="number" name="capacity" min="1" value="<?= (int)$slot['capacity'] ?>" aria-label="Capacity">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <span class="muted"><?= (int)$slot['enrolled'] ?> enrolled</span>
              <button class="btn btn-enroll" type="submit">Save</button>
              <button class="btn btn-cancel" type="submit" name="mode" value="delete">Remove</button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="profile-panel">
        <h2>Equipment</h2>
        <form class="profile-form compact-form" method="post" action="actions/admin_equipment_action.php">
          <input type="hidden" name="mode" value="create">
          <div class="form-grid">
            <input type="text" name="name" placeholder="Equipment name" required>
            <input type="number" name="quantity" min="0" value="1">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <select name="status"><?php foreach ($statuses as $status): ?><option value="<?= htmlspecialchars($status) ?>"><?= htmlspecialchars(ucfirst($status)) ?></option><?php endforeach; ?></select>
          </div>
          <button class="btn-primary" type="submit">Add Equipment</button>
        </form>
        <div class="admin-list">
          <?php foreach ($equipment as $item): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_equipment_action.php">
              <input type="hidden" name="mode" value="update">
              <input type="hidden" name="equipment_id" value="<?= (int)$item['id'] ?>">
              <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" aria-label="Equipment name">
              <input type="number" name="quantity" min="0" value="<?= (int)$item['quantity'] ?>" aria-label="Quantity">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <select name="status" aria-label="Status"><?php foreach ($statuses as $status): ?><option value="<?= htmlspecialchars($status) ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars(ucfirst($status)) ?></option><?php endforeach; ?></select>
              <button class="btn btn-enroll" type="submit">Save</button>
              <button class="btn btn-cancel" type="submit" name="mode" value="delete">Remove</button>
            </form>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="#">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions Â· Regulations Â· Ethics Channel Â· Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>
