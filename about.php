<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — Colorify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <img src="Colorify_Logo.png" alt="Colorify Logo" class="logo">
    <div class="brand">
        <h1>colorify</h1>
        <p>Color Coordinate Generator</p>
    </div>
</header>

<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php" class="active">About</a></li>
        <li><a href="color.php">Color Coordinate</a></li>
    </ul>
</nav>

<main>
    <h2 class="page-title">About Our Team</h2>

    <div class="member-card">
        <img src="Wesley.jpg" alt="Wesley Johnson" class="member-avatar">
        <div class="member-info">
            <h3>Wesley Johnson</h3>
            <p class="member-role">Group Member</p>
            <p>Beyond the classroom, I enjoy playing video games, biking, and running. I am training for an upcoming marathon in May.</p>
        </div>
    </div>

    <!-- Team Member 2 -->
    <div class="member-card">
        <div class="member-avatar-placeholder">&#128100;</div>
        <div class="member-info">
            <h3>[Team Member 2 Name]</h3>
            <p class="member-role">Group Member</p>
            <p>[Team Member 2 bio goes here.]</p>
        </div>
    </div>

    <!-- Team Member 3 -->
    <div class="member-card">
        <div class="member-avatar-placeholder">&#128100;</div>
        <div class="member-info">
            <h3>[Team Member 3 Name]</h3>
            <p class="member-role">Group Member</p>
            <p>[Team Member 3 bio goes here.]</p>
        </div>
    </div>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

</body>
</html>
