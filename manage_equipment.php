<?php
declare(strict_types = 1);

session_start();

require_once __DIR__ . '/controllers/manage_equipment_controller.php';
require_once __DIR__ . '/utils/csrf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: index.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Manage Equipment | Gym4U</title>
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
          <h2>Create Equipment</h2>

          <a href="manage_equipment.php?create-equipment=1" class="btn-create">Add Equipment</a>
        </div>

        <div class="modal-overlay" style="<?= isset($_GET['create-equipment']) ? 'display:flex;' : 'display:none;' ?>">
          <div class="modal-box">
            <a href="manage_equipment.php" class="modal-close">×</a>

            <h2>Create Equipment</h2>

            <form class="profile-form" method="post" action="actions/admin_equipment_action.php">
              <input type="hidden" name="mode" value="create">

              <div class="form-grid">
                <input type="text" name="name" placeholder="Equipment name" required>

                <input type="number" name="quantity" min="0" value="1">

                <select name="status">
                  <?php foreach ($statuses as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>">
                      <?= htmlspecialchars(ucfirst($status)) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>" >

              <button class="btn-primary" type="submit">Add Equipment</button>
            </form>
          </div>
        </div>
    </section>

    <section class="profile-panel">
      <h2>Filter Equipment</h2>

      <form class="filter-group" method="get" action="manage_equipment.php">
        <input
          type="text"
          name="search"
          placeholder="Search equipment"
          value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
        >

        <select name="status">
          <option value="">All Status</option>

          <?php foreach ($statuses as $status): ?>
            <option
              value="<?= htmlspecialchars($status) ?>"
              <?= ($_GET['status'] ?? '') === $status ? 'selected' : '' ?>
            >
              <?= htmlspecialchars(ucfirst($status)) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-filter">
          Apply Filters
        </button>
      </form>
    </section>

    <section class="profile-panel">
      <h2>Manage Equipment</h2>

      <div class="admin-list">
        <?php foreach ($equipment as $item): ?>
          <form class="inline-admin-form" method="post" action="actions/admin_equipment_action.php">
            <input type="hidden" name="mode" value="update">

            <input
              type="hidden"
              name="equipment_id"
              value="<?= (int)$item['id'] ?>"
            >

            <input
              type="text"
              name="name"
              value="<?= htmlspecialchars($item['name']) ?>"
              aria-label="Equipment name"
            >

            <input
              type="number"
              name="quantity"
              min="0"
              value="<?= (int)$item['quantity'] ?>"
              aria-label="Quantity"
            >

            <input
              type="hidden"
              name="csrf_token"
              value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <select name="status" aria-label="Status">
              <?php foreach ($statuses as $status): ?>
                <option
                  value="<?= htmlspecialchars($status) ?>"
                  <?= $item['status'] === $status ? 'selected' : '' ?>
                >
                  <?= htmlspecialchars(ucfirst($status)) ?>
                </option>
              <?php endforeach; ?>
            </select>

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
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="cancellations.php">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>
    <p>Membership Conditions · Regulations · Ethics Channel · Data Processing</p>
    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>
