<?php require_once __DIR__ . '/controllers/manage_schedules_controller.php'; ?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Manage Schedule | Gym4U</title>
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
        <li><a href="actions/logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="profile-panel">
        <div class="admin-header">
          <h2>Create Schedule Slot</h2>

          <a href="manage_schedules.php?create-schedule=1" class="btn-create">Add Schedule</a>
        </div>

        <div class="modal-overlay" style="<?= isset($_GET['create-schedule']) ? 'display:flex;' : 'display:none;' ?>">
          <div class="modal-box">
            <a href="manage_schedules.php" class="modal-close">×</a>

            <h2>Create Schedule Slot</h2>

            <form class="profile-form" method="post" action="actions/admin_schedule_action.php">
              <input type="hidden" name="mode" value="create">

              <div class="form-grid">
                <select name="class_id" required>
                  <?php foreach ($adminClasses as $class): ?>
                    <?php if ($class['name'] !== 'Gym Review'): ?>
                      <option value="<?= (int)$class['id'] ?>">
                        <?= htmlspecialchars($class['name']) ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </select>

                <select name="day">
                  <?php foreach ($days as $day): ?>
                    <option value="<?= htmlspecialchars($day) ?>">
                      <?= htmlspecialchars($day) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <input type="time" name="time" required>
                <input type="number" name="duration_minutes" min="15" value="45" placeholder="Duration">
                <input type="number" name="capacity" min="1" value="12" placeholder="Capacity">
              </div>
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
              <button class="btn-primary" type="submit">Add Schedule Slot</button>
            </form>
          </div>
        </div>
    </section>

    <section class="profile-panel">
      <h2>Filter Schedule</h2>

      <form class="filter-group" method="get" action="manage_schedules.php">
        <select name="class">
          <option value="">All Classes</option>

          <?php foreach ($adminClasses as $class): ?>
            <?php if ($class['name'] !== 'Gym Review'): ?>
              <option
                value="<?= (int)$class['id'] ?>"
                <?= ($_GET['class'] ?? '') === (string)$class['id'] ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($class['name']) ?>
              </option>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>

        <select name="day">
          <option value="">All Days</option>

          <?php foreach ($days as $day): ?>
            <option
              value="<?= htmlspecialchars($day) ?>"
              <?= ($_GET['day'] ?? '') === $day ? 'selected' : '' ?>
            >
              <?= htmlspecialchars($day) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-filter">
          Apply Filters
        </button>
      </form>
    </section>

    <section class="profile-panel">
      <h2>Manage Schedule</h2>

      <div class="admin-list">
        <?php foreach ($adminSchedules as $slot): ?>
          <?php if ($slot['class_name'] !== 'Gym Review'): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_schedule_action.php">
              <input type="hidden" name="mode" value="update">

              <input
                type="hidden"
                name="schedule_id"
                value="<?= (int)$slot['id'] ?>"
              >

              <select name="class_id" aria-label="Class">
                <?php foreach ($adminClasses as $class): ?>
                  <option
                    value="<?= (int)$class['id'] ?>"
                    <?= (int)$slot['class_id'] === (int)$class['id'] ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($class['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <select name="day" aria-label="Day">
                <?php foreach ($days as $day): ?>
                  <option
                    value="<?= htmlspecialchars($day) ?>"
                    <?= $slot['day'] === $day ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($day) ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <input
                type="time"
                name="time"
                value="<?= htmlspecialchars($slot['time']) ?>"
                aria-label="Time"
              >

              <input
                type="number"
                name="duration_minutes"
                min="15"
                value="<?= (int)$slot['duration_minutes'] ?>"
                aria-label="Duration"
              >

              <input
                type="number"
                name="capacity"
                min="1"
                value="<?= (int)$slot['capacity'] ?>"
                aria-label="Capacity"
              >

              <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
              >

              <span class="muted">
                <?= (int)$slot['enrolled'] ?> enrolled
              </span>

              <button class="btn btn-enroll" type="submit">
                Save
              </button>

              <button
                class="btn btn-cancel"
                type="submit"
                name="mode"
                value="delete"
              >
                Remove
              </button>
            </form>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
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
  <script src="js/manage_schedules.js"></script>
</body>
</html>
