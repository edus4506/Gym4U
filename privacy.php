<?php declare(strict_types = 1); ?>

<!DOCTYPE html>
<html lang="en-US">
<head>
  <title>Privacy Policy | Gym4U</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="images/gym4u_icon.png">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/footers.css">
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
        <?php if (isset($_SESSION['user_id'])): ?>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="actions/logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="subscriptions.php">Subscribe</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Privacy Policy</h2>

      <p>
        Please read this Privacy Policy carefully before using the Gym4U website,
        platform, facilities, and digital services. This policy explains how Gym4U
        collects, stores, processes, and protects personal information provided by
        members, trainers, and website visitors while using our services.
      </p>

      <p>
        By accessing the Gym4U platform, creating an account, enrolling in classes,
        booking training sessions, or interacting with any website functionality,
        users acknowledge and accept the practices described in this Privacy Policy.
      </p>
    </section>

    <section class="section terms-section">
      <h2>Privacy and Data Protection</h2>

      <article class="feature">
        <h3>1. Information We Collect</h3>

        <p>
          Gym4U may collect personal information necessary for account management,
          class reservations, trainer scheduling, and overall platform functionality.
        </p>

        <p>
          The information collected may include:
        </p>

        <ul>
          <li>- Full name and profile information;</li>
          <li>- Email address and login credentials;</li>
          <li>- Membership and booking history;</li>
          <li>- Reviews, ratings, and submitted feedback;</li>
          <li>- Usage analytics and interaction data.</li>
        </ul>

        <p>
          Users are responsible for ensuring that all information provided to Gym4U
          is accurate, complete, and kept up to date.
        </p>
      </article>

      <article class="feature">
        <h3>2. Use of Information</h3>

        <p>
          Personal data is processed exclusively for purposes related to the operation,
          improvement, and security of Gym4U services and facilities.
        </p>

        <p>
          Information may be used for:
        </p>

        <ul>
          <li>- Managing memberships and user accounts;</li>
          <li>- Processing class enrollments and trainer bookings;</li>
          <li>- Improving platform functionality and user experience;</li>
          <li>- Communicating important updates and notifications;</li>
          <li>- Ensuring platform security and fraud prevention.</li>
        </ul>

        <p>
          Gym4U does not sell personal information to third parties and processes
          user data exclusively within the scope of platform-related services.
        </p>
      </article>

      <article class="feature">
        <h3>3. Data Storage and Security</h3>

        <p>
          Gym4U implements technical and organizational security measures designed
          to protect personal data against unauthorized access, disclosure, loss,
          misuse, or destruction.
        </p>

        <p>
          Passwords are securely stored using encrypted hashing mechanisms and
          sensitive information is protected through secure authentication practices.
        </p>

        <p>
          Despite reasonable security measures, Gym4U cannot guarantee absolute
          protection against all cyber-security threats, unauthorized access,
          or unforeseen technical incidents.
        </p>
      </article>

      <article class="feature">
        <h3>4. Cookies and Analytics</h3>

        <p>
          Gym4U may use cookies and similar technologies to improve website
          performance, maintain user sessions, and analyze platform usage.
        </p>

        <p>
          Cookies help optimize navigation, personalize user experience,
          and collect anonymous usage statistics related to platform interactions.
        </p>

        <p>
          Users may disable cookies through browser settings, although certain
          platform functionalities may become unavailable or operate incorrectly.
        </p>
      </article>

      <article class="feature">
        <h3>5. Third-Party Services and External Links</h3>

        <p>
          The Gym4U platform may contain references or links to external services
          and third-party websites for informational or functional purposes.
        </p>

        <p>
          Gym4U is not responsible for the privacy practices, policies,
          security standards, or content of external platforms accessed through links.
        </p>

        <p>
          Users are encouraged to review the privacy policies and legal terms
          of all third-party websites they visit.
        </p>
      </article>

      <article class="feature">
        <h3>6. User Rights</h3>

        <p>
          Users may request access, correction, limitation, or deletion of their
          personal data whenever applicable under relevant data protection legislation.
        </p>

        <p>
          Users may also request account deactivation or restriction of specific
          data processing activities related to their information.
        </p>

        <p>
          Requests related to personal data management may require identity verification
          before being processed for security reasons.
        </p>
      </article>

      <article class="feature">
        <h3>7. Data Retention</h3>

        <p>
          Personal information will only be retained for the period necessary
          to fulfill operational, legal, and security requirements associated
          with the Gym4U platform and services.
        </p>

        <p>
          Certain information may be retained temporarily after account deletion
          whenever required for legal compliance, fraud prevention,
          or dispute resolution purposes.
        </p>
      </article>

      <article class="feature">
        <h3>8. Policy Updates</h3>

        <p>
          Gym4U reserves the right to update this Privacy Policy whenever necessary
          to reflect operational, legal, or technical changes.
        </p>

        <p>
          Continued use of the platform after modifications implies acceptance
          of the updated Privacy Policy and associated data practices.
        </p>
      </article>

      <article class="feature">
        <h3>9. Legal Jurisdiction</h3>

        <p>
          This Privacy Policy is governed by applicable Portuguese legislation
          and European Union data protection principles.
        </p>

        <p>
          Any disputes related to the processing of personal data or use of the
          Gym4U platform shall fall under the jurisdiction of the competent
          courts in Portugal.
        </p>
      </article>
    </section>
  </main>

  <footer class="footer">
    <p>
      <a href="privacy.php">Privacy Policy</a> |
      <a href="terms.php">Terms of Use</a> |
      <a href="#">Cancellations</a> |
      <a href="https://www.livroreclamacoes.pt/Inicio/" target="_blank"> Complaints Book </a>
    </p>

    <p>
      Membership Conditions · Regulations · Ethics Channel · Data Processing
    </p>

    <p>&copy; 2026 Gym4U. All rights reserved.</p>
  </footer>
</body>
</html>