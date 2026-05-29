<?php require_once __DIR__ . '/controllers/manage_users_controller.php'; ?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Manage Users | Gym4U</title>
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
        <li>
          <form action="actions/logout.php" method="post" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button type="submit">Logout</button>
          </form>
        </li>
      </ul>
    </nav>
  </header>
  <main>
    <section class="profile-panel">
        <div class="admin-header">
          <h2>Create New Member, Trainer or Admin</h2>

          <a href="manage_users.php?create-user=1" class="btn-create">Create Account</a>
        </div>

        <div class="modal-overlay" style="<?= isset($_GET['create-user']) ? 'display:flex;' : 'display:none;' ?>">
          <div class="modal-box">
            <a href="manage_users.php" class="modal-close">×</a>

            <h2>Create Account</h2>

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
          </div>
        </div>
    </section>

    <section class="profile-panel">
      <h2>Filter Users</h2>

      <form class="filter-group" method="get" action="manage_users.php">
        <input type="text" name="search" placeholder="Search by name" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

        <select name="role">
          <option value="">All Roles</option>

          <?php foreach (['member', 'trainer', 'admin'] as $role): ?>
            <option
              value="<?= htmlspecialchars($role) ?>"
              <?= ($_GET['role'] ?? '') === $role ? 'selected' : '' ?>
            >
              <?= htmlspecialchars(ucfirst($role)) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select name="active">
          <option value="">All Status</option>

          <option value="1" <?= ($_GET['active'] ?? '') === '1' ? 'selected' : '' ?>>
            Active
          </option>

          <option value="0" <?= ($_GET['active'] ?? '') === '0' ? 'selected' : '' ?>>
            Inactive
          </option>
        </select>

        <button type="submit" class="btn-filter">
          Apply Filters
        </button>
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
  <script src="js/manage_users.js"></script>
</body>
</html>