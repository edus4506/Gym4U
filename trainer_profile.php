<?php 
    require_once __DIR__ . '/controllers/headerController.php';
    require_once __DIR__ . '/controllers/trainerProfileController.php';    
?>

<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($trainer['name']) ?> | Gym4U</title>
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
        <label for="menu-toggle"  class="hamburger">☰</label>
        <nav class="right" aria-label="Main navigation">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="classes.php">Classes</a></li>
                <li><a href="trainers.php">Trainers</a></li>
                <li><a href="equipment.php">Equipment</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Profile</a></li>
                <?php if ($role == 'member' && $subscriptionPlan == 'none'): ?>
                    <li><a href="subscriptions.php">Subscribe</a></li>
                <?php endif; ?>
                    <li>
                        <form action="actions/logout.php" method="post" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                            <button type="submit">Logout</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="trainers-page">
        <section class="intro-panel intro-panel-dark">
            <h2><?=  htmlspecialchars($trainer['name']) ?></h2>
            <p><?= htmlspecialchars($trainer['title']) ?></p>
        </section>

        <section>
            <div class="trainer-profile-header">
                <img
                    class="trainer-profile-img"
                    src="images/trainers/<?= htmlspecialchars(strtolower(str_replace(' ', '-', $trainer['name']))) ?>.png"
                    alt="Portrait of <?= htmlspecialchars($trainer['name']) ?>"
                    onerror="this.style.display='none'"
                >
                <div class="trainer-profile-info">
                    <p class="trainer-meta"><strong>Experience:</strong> <?= (int)$trainer['experience_years'] ?> years</p>
                    <p class="trainer-meta"><strong>Certifications:</strong> <?= htmlspecialchars($trainer['certifications']) ?></p>
                    <p class="trainer-meta"><strong>Specializations:</strong> <?= htmlspecialchars($trainer['specializations']) ?></p>
                    <p><?= htmlspecialchars($trainer['bio']) ?></p>
                    <p class="trainer-meta"><strong>Phone:</strong> <?= htmlspecialchars($trainer['phone']) ?></p>
                    <p class="trainer-meta"><strong>Email:</strong>
                        <a href="mailto:<?= htmlspecialchars($trainer['email']) ?>"><?= htmlspecialchars($trainer['email']) ?></a>
                    </p>
                </div>
            </div>
        </section>

        <section>
            <h2>Classes</h2>
            <div class="trainer-grid">
                <?php foreach ($trainerClasses as $class): ?>
                    <article class="trainer-card">
                        <div class="trainer-content">
                            <h3><?= htmlspecialchars($class['name']) ?></h3>
                            <p class="trainer-role"><?= htmlspecialchars($class['category']) ?></p>
                            <p><?= htmlspecialchars($class['description']) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2>Weekly Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Class</th>
                        <th scope="col">Day</th>
                        <th scope="col">Time</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Capacity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trainerSchedule as $slot): ?>
                        <tr>
                            <th scope="row"><?= htmlspecialchars($slot['class_name']) ?></th>
                            <td><?= htmlspecialchars($slot['day']) ?></td>
                            <td><?= htmlspecialchars($slot['time']) ?></td>
                            <td><?= (int)$slot['duration_minutes'] ?> min</td>
                            <td><?= (int)$slot['enrolled'] ?>/<?= (int)$slot['capacity'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
</body>
</html>