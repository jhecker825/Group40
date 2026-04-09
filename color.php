<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Coordinating</title>
    <link rel="stylesheet" href="style.css">
    <script src="color.js" defer></script>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <h1 class="logo-text">[LOGO]</h1>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="color.php" class="active">Color Coordinating</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <section class="page-header">
            <h1>Color Coordinate Generator</h1>
            <p>Create your custom color coordinate sheets</p>
        </section>

        <section class="form-section">
            <form id="colorForm" method="POST" action="">
                <div class="form-group">
                    <label for="gridSize">Rows and Columns (1-26):</label>
                    <input type="number" id="gridSize" name="gridSize" min="1" max="26" value="5" required>
                </div>
                <div class="form-group">
                    <label for="colorCount">Number of Colors (1-10):</label>
                    <input type="number" id="colorCount" name="colorCount" min="1" max="10" value="3" required>
                </div>
                <button type="submit" class="btn-submit">Generate Coordinate Sheet</button>
            </form>

            <div class="error-messages">
                <div id="gridSizeError" class="error-message" style="display: none;">
                    Error: Rows and Columns must be between 1 and 26.
                </div>
                <div id="colorCountError" class="error-message" style="display: none;">
                    Error: Number of Colors must be between 1 and 10.
                </div>
                <div id="duplicateColorMessage" class="warning-message" style="display: none;">
                    Color is already in use. Please select a different color.
                </div>
            </div>
        </section>

        <section id="tablesSection" class="tables-section" style="display: none;">
            <h2>Selected Colors</h2>
            <table id="colorTable" class="color-table">
                <tbody id="colorTableBody">
                </tbody>
            </table>

            <h2>Coordinate Grid</h2>
            <table id="gridTable" class="grid-table">
                <tbody id="gridTableBody">
                </tbody>
            </table>

            <div class="button-section">
                <form method="POST" action="print.php">
                    <input type="hidden" id="printGridSize" name="gridSize">
                    <input type="hidden" id="printColorCount" name="colorCount">
                    <input type="hidden" id="printColors" name="colors">
                    <button type="submit" class="btn-print">View Printable Version</button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 [COMPANY]. All rights reserved.</p>
    </footer>
</body>
</html>
