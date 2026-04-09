<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Coordinate — Colorify</title>
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
        <li><a href="about.php">About</a></li>
        <li><a href="color.php" class="active">Color Coordinate</a></li>
    </ul>
</nav>

<main>
    <h2 class="page-title">Color Coordinate Generator</h2>

    <?php
    $n         = null;
    $numColors = null;
    $errors    = [];
    $submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

    if ($submitted) {
        $rawN  = $_POST['grid_size']   ?? '';
        $rawNC = $_POST['num_colors']  ?? '';

        if ($rawN === '' || !is_numeric($rawN) || (int)$rawN < 1 || (int)$rawN > 26) {
            $errors[] = 'Rows &amp; Columns must be a number between 1 and 26.';
        } else {
            $n = (int)$rawN;
        }

        if ($rawNC === '' || !is_numeric($rawNC) || (int)$rawNC < 1 || (int)$rawNC > 10) {
            $errors[] = 'Number of Colors must be a number between 1 and 10.';
        } else {
            $numColors = (int)$rawNC;
        }
    }

    $colorOptions = ['Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Grey', 'Brown', 'Black', 'Teal'];

    $colorHex = [
        'Red'    => '#DC2626',
        'Orange' => '#EA580C',
        'Yellow' => '#CA8A04',
        'Green'  => '#16A34A',
        'Blue'   => '#2563EB',
        'Purple' => '#7C3AED',
        'Grey'   => '#6B7280',
        'Brown'  => '#92400E',
        'Black'  => '#111827',
        'Teal'   => '#0D9488',
    ];
    ?>

    <div class="card">
        <form method="post" action="color.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="grid_size">Rows &amp; Columns</label>
                    <input type="number" id="grid_size" name="grid_size" min="1" max="26"
                           value="<?php echo htmlspecialchars($_POST['grid_size'] ?? ''); ?>" required>
                    <span class="form-hint">Enter a value from 1 to 26</span>
                </div>
                <div class="form-group">
                    <label for="num_colors">Number of Colors</label>
                    <input type="number" id="num_colors" name="num_colors" min="1" max="10"
                           value="<?php echo htmlspecialchars($_POST['num_colors'] ?? ''); ?>" required>
                    <span class="form-hint">Enter a value from 1 to 10</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo $err; ?></div>
    <?php endforeach; ?>

    <?php if ($submitted && empty($errors)): ?>

        <div id="color-warning" class="alert alert-info">
            That color is already selected. Please choose a different one.
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem;">Selected Colors</h3>
            <table id="color-list-table">
                <?php for ($i = 0; $i < $numColors; $i++):
                    $selected = $colorOptions[$i];
                ?>
                <tr>
                    <td class="col-dropdown">
                        <select id="color-select-<?php echo $i; ?>" data-index="<?php echo $i; ?>">
                            <?php foreach ($colorOptions as $color): ?>
                                <option value="<?php echo $color; ?>"
                                    <?php echo ($color === $selected) ? 'selected' : ''; ?>>
                                    <?php echo $color; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td class="col-preview">
                        <div class="color-preview-cell">
                            <span class="color-swatch" id="swatch-<?php echo $i; ?>"
                                  style="background-color: <?php echo $colorHex[$selected]; ?>;"></span>
                            <span class="color-label" id="label-<?php echo $i; ?>"><?php echo $selected; ?></span>
                        </div>
                    </td>
                </tr>
                <?php endfor; ?>
            </table>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem;">Coordinate Grid (<?php echo $n; ?> &times; <?php echo $n; ?>)</h3>
            <div id="grid-table-wrap">
                <table id="grid-table">
                    <?php
                    $letters = range('A', 'Z');
                    for ($row = 0; $row <= $n; $row++):
                    ?>
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
            </div>
        </div>

        <div class="print-section">
            <button class="btn btn-secondary" onclick="goToPrint()">View Printable Version</button>
        </div>

        <?php
        $colorHexJson = json_encode($colorHex);
        ?>

        <script>
        const COLOR_HEX = <?php echo $colorHexJson; ?>;
        const numSelects = <?php echo $numColors; ?>;

        function getSelected() {
            const vals = [];
            for (let i = 0; i < numSelects; i++) {
                vals.push(document.getElementById('color-select-' + i).value);
            }
            return vals;
        }

        function updatePreview(index, colorName) {
            document.getElementById('swatch-' + index).style.backgroundColor = COLOR_HEX[colorName];
            document.getElementById('label-' + index).textContent = colorName;
        }

        function showWarning() {
            const el = document.getElementById('color-warning');
            el.style.display = 'block';
            clearTimeout(el._hideTimer);
            el._hideTimer = setTimeout(() => { el.style.display = 'none'; }, 3500);
        }

        for (let i = 0; i < numSelects; i++) {
            const sel = document.getElementById('color-select-' + i);
            sel._prev = sel.value;

            sel.addEventListener('change', function () {
                const chosen = this.value;
                const myIndex = parseInt(this.dataset.index);
                const allVals = getSelected();

                let duplicate = false;
                for (let j = 0; j < numSelects; j++) {
                    if (j !== myIndex && allVals[j] === chosen) {
                        duplicate = true;
                        break;
                    }
                }

                if (duplicate) {
                    this.value = this._prev;
                    showWarning();
                } else {
                    this._prev = chosen;
                    updatePreview(myIndex, chosen);
                }
            });
        }

        function goToPrint() {
            const n  = <?php echo $n; ?>;
            const nc = <?php echo $numColors; ?>;
            const colors = getSelected();
            const params = new URLSearchParams();
            params.set('n', n);
            params.set('nc', nc);
            colors.forEach(c => params.append('color[]', c));
            window.location.href = 'print.php?' + params.toString();
        }
        </script>

    <?php endif; ?>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

</body>
</html>
