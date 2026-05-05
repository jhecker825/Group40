<?php
require 'db.php';

$dbColors = $pdo->query('SELECT id, name, hex_value FROM colors ORDER BY name ASC')->fetchAll();
$maxColors = count($dbColors);

$n = null;
$numColors = null;
$errors = [];
$submitted = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($maxColors < 1) {
    $errors[] = 'no colors found in the database add colors first';
}

if ($submitted) {
    $rawN = $_POST['grid_size'] ?? '';
    $rawNC = $_POST['num_colors'] ?? '';

    if ($rawN === '' || !is_numeric($rawN) || (int)$rawN < 1 || (int)$rawN > 26) {
        $errors[] = 'rows and columns must be a number between 1 and 26';
    } else {
        $n = (int)$rawN;
    }

    if ($rawNC === '' || !is_numeric($rawNC) || (int)$rawNC < 1 || (int)$rawNC > $maxColors) {
        $errors[] = 'number of colors must be a number between 1 and ' . $maxColors;
    } else {
        $numColors = (int)$rawNC;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Coordinate - Colorify</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="colors_dynamic.php">
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
        <li><a href="colors.php">Color Selection</a></li>
    </ul>
</nav>

<main>
    <h2 class="page-title">Color Coordinate Generator</h2>

    <div class="card">
        <form method="post" action="color.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="grid_size">Rows &amp; Columns</label>
                    <input
                        type="number"
                        id="grid_size"
                        name="grid_size"
                        min="1"
                        max="26"
                        value="<?php echo htmlspecialchars($_POST['grid_size'] ?? ''); ?>"
                        required
                    >
                    <span class="form-hint">enter a value from 1 to 26</span>
                </div>
                <div class="form-group">
                    <label for="num_colors">Number of Colors</label>
                    <input
                        type="number"
                        id="num_colors"
                        name="num_colors"
                        min="1"
                        max="<?php echo $maxColors > 0 ? $maxColors : 1; ?>"
                        value="<?php echo htmlspecialchars($_POST['num_colors'] ?? ''); ?>"
                        required
                    >
                    <span class="form-hint">enter a value from 1 to <?php echo $maxColors; ?></span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>

    <?php if ($submitted && empty($errors)): ?>
        <?php
        $letters = range('A', 'Z');
        $rows = array_slice($dbColors, 0, $numColors);

        $colorMap = [];
        foreach ($dbColors as $c) {
            $id = (int)$c['id'];
            $colorMap[$id] = [
                'name' => $c['name'],
                'hex' => strtoupper($c['hex_value'])
            ];
        }
        ?>

        <div id="color-warning" class="alert alert-info">
            that color is already selected pick a different one
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem;">Selected Colors</h3>
            <table id="color-list-table">
                <?php for ($i = 0; $i < $numColors; $i++):
                    $selected = $rows[$i];
                    $selectedId = (int)$selected['id'];
                    $selectedName = $selected['name'];
                    $selectedHex = strtoupper($selected['hex_value']);
                ?>
                    <tr>
                        <td class="col-radio">
                            <input
                                type="radio"
                                id="color-radio-<?php echo $i; ?>"
                                name="active-color"
                                value="<?php echo $i; ?>"
                                <?php echo ($i === 0) ? 'checked' : ''; ?>
                            >
                        </td>
                        <td class="col-dropdown">
                            <select id="color-select-<?php echo $i; ?>" data-index="<?php echo $i; ?>">
                                <?php foreach ($dbColors as $color): ?>
                                    <?php
                                    $id = (int)$color['id'];
                                    $name = $color['name'];
                                    $hex = strtoupper($color['hex_value']);
                                    ?>
                                    <option
                                        value="<?php echo $id; ?>"
                                        data-name="<?php echo htmlspecialchars($name); ?>"
                                        data-hex="<?php echo htmlspecialchars($hex); ?>"
                                        <?php echo ($id === $selectedId) ? 'selected' : ''; ?>
                                    >
                                        <?php echo htmlspecialchars($name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="col-preview">
                            <div class="color-preview-cell">
                                <span class="color-swatch" id="swatch-<?php echo $i; ?>" style="background-color: <?php echo htmlspecialchars($selectedHex); ?>;"></span>
                                <span class="color-label" id="label-<?php echo $i; ?>"><?php echo htmlspecialchars($selectedName); ?> (<?php echo htmlspecialchars($selectedHex); ?>)</span>
                            </div>
                        </td>
                        <td class="col-coordinates">
                            <span id="coordinates-<?php echo $i; ?>"></span>
                        </td>
                    </tr>
                <?php endfor; ?>
            </table>
        </div>

        <div class="card">
            <h3 style="margin-bottom:1rem;">Coordinate Grid (<?php echo $n; ?> x <?php echo $n; ?>)</h3>
            <div id="grid-table-wrap">
                <table id="grid-table">
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
                                    <td class="grid-cell" data-coord="<?php echo $letters[$col - 1] . $row; ?>"></td>
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

        <script>
        const colorMap = <?php echo json_encode($colorMap); ?>;
        const numSelects = <?php echo $numColors; ?>;
        const colorCoordinates = {};

        for (let i = 0; i < numSelects; i++) {
            colorCoordinates[i] = [];
        }

        function getSelectEl(index) {
            return document.getElementById('color-select-' + index);
        }

        function getSelectedIds() {
            const vals = [];
            for (let i = 0; i < numSelects; i++) {
                vals.push(parseInt(getSelectEl(i).value, 10));
            }
            return vals;
        }

        function getActiveColorRowIndex() {
            const checked = document.querySelector('input[name="active-color"]:checked');
            return checked ? parseInt(checked.value, 10) : 0;
        }

        function getSelectedColorId(rowIndex) {
            return parseInt(getSelectEl(rowIndex).value, 10);
        }

        function updatePreview(rowIndex, colorId) {
            const swatch = document.getElementById('swatch-' + rowIndex);
            const label = document.getElementById('label-' + rowIndex);
            const item = colorMap[colorId];
            if (!item) return;
            swatch.style.backgroundColor = item.hex;
            label.textContent = item.name + ' (' + item.hex + ')';
        }

        function showWarning() {
            const el = document.getElementById('color-warning');
            el.style.display = 'block';
            clearTimeout(el._hideTimer);
            el._hideTimer = setTimeout(() => {
                el.style.display = 'none';
            }, 3000);
        }

        function sortCoordinates(coords) {
            return coords.sort((a, b) => {
                const letterA = a.charCodeAt(0);
                const letterB = b.charCodeAt(0);
                if (letterA !== letterB) return letterA - letterB;
                const numA = parseInt(a.substring(1), 10);
                const numB = parseInt(b.substring(1), 10);
                return numA - numB;
            });
        }

        function updateCoordinatesDisplay(rowIndex) {
            const coords = colorCoordinates[rowIndex];
            const sorted = sortCoordinates([...coords]);
            document.getElementById('coordinates-' + rowIndex).textContent = sorted.join(', ');
        }

        function removePaintClass(cell) {
            const toRemove = [];
            cell.classList.forEach(cls => {
                if (cls.startsWith('paint-color-')) {
                    toRemove.push(cls);
                }
            });
            toRemove.forEach(cls => cell.classList.remove(cls));
        }

        function paintCell(coord, rowIndex) {
            const cell = document.querySelector(`[data-coord="${coord}"]`);
            if (!cell) return;

            const colorId = getSelectedColorId(rowIndex);
            removePaintClass(cell);
            cell.classList.add('paint-color-' + colorId);
            cell.dataset.paintedRow = String(rowIndex);
            cell.dataset.paintedColorId = String(colorId);
        }

        function clearCell(coord) {
            const cell = document.querySelector(`[data-coord="${coord}"]`);
            if (!cell) return;
            removePaintClass(cell);
            delete cell.dataset.paintedRow;
            delete cell.dataset.paintedColorId;
        }

        function addCoordinate(rowIndex, coord) {
            if (!colorCoordinates[rowIndex].includes(coord)) {
                colorCoordinates[rowIndex].push(coord);
            }
            updateCoordinatesDisplay(rowIndex);
        }

        function removeCoordinate(rowIndex, coord) {
            const idx = colorCoordinates[rowIndex].indexOf(coord);
            if (idx > -1) {
                colorCoordinates[rowIndex].splice(idx, 1);
            }
            updateCoordinatesDisplay(rowIndex);
        }

        document.querySelectorAll('.grid-cell').forEach(cell => {
            cell.addEventListener('click', function () {
                const coord = this.dataset.coord;
                const activeRowIndex = getActiveColorRowIndex();

                if (this.dataset.paintedRow === undefined) {
                    paintCell(coord, activeRowIndex);
                    addCoordinate(activeRowIndex, coord);
                    return;
                }

                const currentRowIndex = parseInt(this.dataset.paintedRow, 10);
                if (currentRowIndex === activeRowIndex) {
                    clearCell(coord);
                    removeCoordinate(currentRowIndex, coord);
                } else {
                    removeCoordinate(currentRowIndex, coord);
                    paintCell(coord, activeRowIndex);
                    addCoordinate(activeRowIndex, coord);
                }
            });
        });

        for (let i = 0; i < numSelects; i++) {
            const sel = getSelectEl(i);
            sel.dataset.prev = sel.value;

            sel.addEventListener('change', function () {
                const myIndex = parseInt(this.dataset.index, 10);
                const newColorId = parseInt(this.value, 10);
                const selectedIds = getSelectedIds();

                let duplicate = false;
                for (let j = 0; j < numSelects; j++) {
                    if (j !== myIndex && selectedIds[j] === newColorId) {
                        duplicate = true;
                        break;
                    }
                }

                if (duplicate) {
                    this.value = this.dataset.prev;
                    showWarning();
                    return;
                }

                const coordsToRepaint = [...colorCoordinates[myIndex]];
                coordsToRepaint.forEach(coord => {
                    paintCell(coord, myIndex);
                });

                this.dataset.prev = this.value;
                updatePreview(myIndex, newColorId);
            });
        }

        function goToPrint() {
            const params = new URLSearchParams();
            params.set('n', String(<?php echo $n; ?>));
            params.set('nc', String(numSelects));

            for (let i = 0; i < numSelects; i++) {
                const colorId = getSelectedColorId(i);
                const item = colorMap[colorId] || { name: '', hex: '#000000' };
                const sorted = sortCoordinates([...colorCoordinates[i]]);

                params.append('color_name[]', item.name);
                params.append('color_hex[]', item.hex);
                params.append('coords[]', sorted.join(', '));
            }

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
