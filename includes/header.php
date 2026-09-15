<?php
// includes/header.php
// Site header navigation and global HTML shell.

require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tandem | Premium Freelance Network</title>
  
  <!-- Preconnect and link Google Fonts (Fraunces & Inter) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  
  <!-- Link custom design system CSS -->
  <link rel="stylesheet" href="assets/css/style-guide.css">
  <style>
    .app-navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: var(--space-16) var(--space-24);
      background-color: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--color-border);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: var(--shadow-subtle);
    }
    .app-logo {
      font-family: var(--font-heading);
      font-size: var(--text-h4);
      font-weight: 600;
      color: var(--color-primary);
      text-decoration: none;
    }
    .nav-links {
      display: flex;
      gap: var(--space-24);
      align-items: center;
    }
    .nav-links a {
      color: var(--color-text-neutral);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }
    .nav-links a:hover {
      color: var(--color-primary);
    }
    
    .page-main {
      min-height: calc(100vh - 200px);
    }
  </style>
</head>
<body>

  <!-- Global Header Navigation -->
  <header class="app-navbar">
    <a href="index.php" class="app-logo">Tandem</a>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="services.php">Services</a>
      <a href="contact.php">Contact</a>
      <span style="color: var(--color-border);">|</span>
      
      <?php if ($user): ?>
        <?php
          $dashUrl = 'client-dashboard.php';
          if ($user['role'] === 'freelancer') $dashUrl = 'freelancer-dashboard.php';
          if ($user['role'] === 'admin') $dashUrl = 'admin-dashboard.php';
        ?>
        <a href="<?php echo $dashUrl; ?>" style="font-weight: 600; color: var(--color-primary);">
          Dashboard (<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>)
        </a>
        <a href="logout.php" class="btn btn-secondary" style="padding: var(--space-8) var(--space-16);">Log Out</a>
      <?php else: ?>
        <a href="login.php">Log In</a>
        <a href="register.php" class="btn btn-primary" style="padding: var(--space-8) var(--space-16); color: white;">Sign Up</a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- Global Flash Messages Banner -->
  <?php renderFlashMessages(); ?>
