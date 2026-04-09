<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colorify — Color Coordinate Generator</title>
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
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="color.php">Color Coordinate</a></li>
    </ul>
</nav>

<main>
    <div class="hero">
        <img src="img/Colorify_Logo.png" alt="Colorify Logo" class="hero-logo">
        <h2>Welcome to Colorify</h2>
        <p>
            Colorify is a professional color coordinate sheet generator. Build custom grids,
            organize your color palettes, and generate printable coordinate sheets — all in one place.
        </p>
    </div>

    <div class="nav-cards">
        <a href="about.php" class="nav-card">
            <div class="card-icon">&#128101;</div>
            <h3>About Us</h3>
            <p>Meet the team behind Colorify.</p>
        </a>
        <a href="color.php" class="nav-card">
            <div class="card-icon">&#127775;</div>
            <h3>Color Coordinate</h3>
            <p>Generate a custom color coordinate sheet with your chosen grid size and colors.</p>
        </a>
    </div>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

</body>
</html>
