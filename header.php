<?php
?>
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Italiensk mat</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" href="stylesheet">
    <!-- Import google fonts -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <h1>Italienske Mattradisjoner</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Hjem</a></li>
                <li><a href="tradisjoner.php">Italienske Tradisjoner</a></li>
                <li><a href="menu.php">Meny</a></li>

                <?php if (isset($_SESSION['idusers'])): ?>

                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                       <li><a href="admin.php" class="admin-link">Administrasjon</a></li>
                    <?php endif; ?>
                    <li><a href ="logout.php">Logg ut</a></li>
                <?php else: ?>

                    <li><a href="login.php">Logg inn</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
