<?php
session_start();

require_once __DIR__ . '/controllers/equipmentController.php';
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Equipment | Gym4U</title>
    <link rel="icon" href="images/gym4u_icon.png">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/equipment.css">
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
                <li><a href="equipment.php" aria-current="page">Equipment</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="actions/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="subscriptions.php">Subscribe</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="main">
        <section class="section">
            <h2>Equipment</h2>
            <p>Check the availability of all gym equipment below.</p>
        </section>
        <section class="section">
            <form method="get" action="equipment.php" class="filter-form">
                <label for="status">Filter by status:</label>
                <select name="status" id="status">
                    <option value="all" <?= $statusFilter === 'all' ? 'selected': '' ?>>All</option>
                    <option value="available" <?= $statusFilter === 'available' ? 'selected': '' ?>>Available</option>
                    <option value="maintenance" <?= $statusFilter === 'maintenance' ? 'selected': '' ?>>Maintenance</option>
                    <option value="unavailable" <?= $statusFilter === 'unavailable' ? 'selected': '' ?>>Unavailable</option>
                </select>
                <button type="submit" class="btn-primary">Filter</button>
            </form>
        </section>
        <section class="section">
            <table class="table">
                <thead class="thead">
                    <tr class="tr">
                        <th class="th" scope="col">Equipment</th>
                        <th class="th" scope="col">Quantity</th>
                        <th class="th" scope="col">Status</th>
                    </tr>
                </thead>
                <tbody class="tbody">
                    <?php foreach ($equipment as $item): ?>
                        <tr class="tr">
                            <th class="th" scope="row"><?= htmlspecialchars($item['name']) ?></th>
                            <td class="td"><?= (int)($item['quantity']) ?></td>
                            <td class="td">
                                <span class="badge badge-<?= htmlspecialchars($item['status']) ?>">
                                    <?= htmlspecialchars(ucfirst($item['status'])) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

                    
                    

    
