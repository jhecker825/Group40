<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print View — Colorify</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body.print-page main {
            max-width: 100%;
            padding: 1cm;
        }
        @page {
            size: letter portrait;
            margin: 1cm;
        }
    </style>
</head>
<body class="print-page">

<main>

    <?php
    $n         = isset($_GET['n'])  ? max(1, min(26, (int)$_GET['n']))  : 1;
    $numColors = isset($_GET['nc']) ? max(1, min(10, (int)$_GET['nc'])) : 1;
    $colors    = isset($_GET['color']) ? (array)$_GET['color'] : [];

    $validColors = ['Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Grey', 'Brown', 'Black', 'Teal'];

    $colors = array_values(array_filter($colors, fn($c) => in_array($c, $validColors)));

    while (count($colors) < $numColors) {
        foreach ($validColors as $vc) {
            if (!in_array($vc, $colors)) {
                $colors[] = $vc;
                break;
            }
        }
    }
    $colors = array_slice($colors, 0, $numColors);
    ?>

    <div class="print-header">
        <img src="img/Colorify_Logo.png" alt="Colorify Logo">
        <div class="print-brand">
            <h1>colorify</h1>
            <p>Color Coordinate Generator</p>
        </div>
    </div>

    <table class="print-color-table">
        <?php for ($i = 0; $i < $numColors; $i++): ?>
        <tr>
            <td class="col-dropdown"><?php echo htmlspecialchars($colors[$i]); ?></td>
            <td class="col-preview"><?php echo htmlspecialchars($colors[$i]); ?></td>
        </tr>
        <?php endfor; ?>
    </table>

    <?php
    $letters = range('A', 'Z');
    ?>
    <table class="print-grid-table">
        <?php for ($row = 0; $row <= $n; $row++): ?>
        <tr>
            <?php for ($col = 0; $col <= $n; $col++): ?>
                <?php if ($row === 0 && $col === 0): ?>
                    <td class="corner-cell"></td>
                <?php elseif ($row === 0): ?>
                    <td class="header-cell"><?php echo $letters[$col - 1]; ?></td>
                <?php elseif ($col === 0): ?>
                    <td class="header-cell"><?php echo $row; ?></td>
                <?php else: ?>
                    <td></td>
                <?php endif; ?>
            <?php endfor; ?>
        </tr>
        <?php endfor; ?>
    </table>

    <div class="no-print" style="margin-top:1.5rem; text-align:center;">
        <button class="btn btn-primary" onclick="window.print()">Print This Page</button>
        <a href="color.php" class="btn btn-secondary" style="margin-left:0.75rem;">Back to Generator</a>
    </div>

</main>

</body>
</html>
