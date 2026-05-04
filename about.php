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
    <img src="img/Colorify_Logo.png" alt="Colorify Logo" class="logo">
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
        <li><a href="colors.php">Color Selection</a></li>
    </ul>
</nav>

<main>
    <h2 class="page-title">About Our Team</h2>

    <div class="member-card">
        <img src="img/Wesley.jpg" alt="Wesley Johnson" class="member-avatar">
        <div class="member-info">
            <h3>Wesley Johnson</h3>
            <p class="member-role">Group Member</p>
            <p>Beyond the classroom, I enjoy playing video games, biking, and running. I am training for an upcoming marathon in May.</p>
        </div>
    </div>

    <div class="member-card">
        <img src="img/Joseph.jpg" alt="Joseph Hecker" class="member-avatar">
        <div class="member-info">
            <h3>Joseph Hecker</h3>
            <p class="member-role">Group Member</p>
            <p>Hello, I'm Joseph Hecker, a computer science student as CSU and I am currently a senior. I am also a software engineer at CartoPac, where I work on GIS mapping software. I have experience in C# and C++.</p>
        </div>
    </div>

    <!-- Team Member 3 -->
    <div class="member-card">
        <img src="img/Nathan.png" alt="Nathan Anderson" class="member-avatar">
        <div class="member-info">
            <h3>Nathan Anderson</h3>
            <p class="member-role">Group Member</p>
            <p>I enjoy video games and traveling. I also like to work on coding projects. I play for the CSU deadlock team and my favorite country I've been to is Sweden.</p>
        </div>
    </div>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

</body>
</html>
