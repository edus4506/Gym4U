<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/controllers/manage_classes_controller.php';
require_once __DIR__ . '/utils/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Manage Classes | Gym4U</title>
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
          <h2>Create New Class</h2>

          <a href="manage_classes.php?create-class=1" class="btn-create">Create Class</a>
        </div>

        <div class="modal-overlay" style="<?= isset($_GET['create-class']) ? 'display:flex;' : 'display:none;' ?>">
          <div class="modal-box">
            <a href="manage_classes.php" class="modal-close">×</a>

            <h2>Create Class</h2>

            <form class="profile-form" method="post" action="actions/admin_class_action.php">
              <input type="hidden" name="mode" value="create">

              <div class="form-grid">
                <input type="text" name="name" placeholder="Class name" required>

                <input type="text" name="description" placeholder="Description">

                <select name="category_id" required>
                  <?php foreach ($categories as $category): ?>
                    <option value="<?= (int)$category['id'] ?>">
                      <?= htmlspecialchars($category['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <select name="trainer_id" required>
                  <?php foreach ($trainers as $trainer): ?>
                    <option value="<?= (int)$trainer['id'] ?>">
                      <?= htmlspecialchars($trainer['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

              <button class="btn-primary" type="submit">Create Class</button>
            </form>
          </div>
        </div>
    </section>

    <section class="profile-panel">
      <h2>Filter Classes</h2>

      <form class="filter-group" method="get" action="manage_classes.php">
        <input
          type="text"
          name="search"
          placeholder="Search by class name"
          value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        >

        <select name="category">
          <option value="">All Categories</option>

          <?php foreach ($categories as $category): ?>
            <option
              value="<?= (int)$category['id'] ?>"
              <?= ($_GET['category'] ?? '') === (string)$category['id'] ? 'selected' : '' ?>
            >
              <?= htmlspecialchars($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select name="trainer">
          <option value="">All Trainers</option>

          <?php foreach ($trainers as $trainer): ?>
            <option
              value="<?= (int)$trainer['id'] ?>"
              <?= ($_GET['trainer'] ?? '') === (string)$trainer['id'] ? 'selected' : '' ?>
            >
              <?= htmlspecialchars($trainer['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-filter">
          Apply Filters
        </button>
      </form>
    </section>

    <section class="profile-panel">
      <h2>Manage Classes</h2>

      <div class="admin-list">
        <?php foreach ($adminClasses as $class): ?>
          <?php if ($class['name'] !== 'Gym Review'): ?>
            <form class="inline-admin-form" method="post" action="actions/admin_class_action.php">
              <input type="hidden" name="mode" value="update">

              <input type="hidden" name="class_id" value="<?= (int)$class['id'] ?>">

              <input
                type="text"
                name="name"
                value="<?= htmlspecialchars($class['name']) ?>"
                aria-label="Class name"
              >

              <input
                type="text"
                name="description"
                value="<?= htmlspecialchars($class['description']) ?>"
                aria-label="Description"
              >

              <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
              >

              <select name="category_id" aria-label="Category">
                <?php foreach ($categories as $category): ?>
                  <option
                    value="<?= (int)$category['id'] ?>"
                    <?= (int)$class['category_id'] === (int)$category['id'] ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($category['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <select name="trainer_id" aria-label="Trainer">
                <?php foreach ($trainers as $trainer): ?>
                  <option
                    value="<?= (int)$trainer['id'] ?>"
                    <?= (int)$class['trainer_id'] === (int)$trainer['id'] ? 'selected' : '' ?>
                  >
                    <?= htmlspecialchars($trainer['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <button class="btn btn-enroll" type="submit">
                Save
              </button>

              <button class="btn btn-cancel" type="submit" name="mode" value="delete">
                Remove
              </button>
            </form>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancelations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank">
        Complaints Book
      </a>
    </p>

    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>

    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>
