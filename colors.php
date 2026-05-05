<?php
require 'db.php';

$errors = [];
$success = '';
$action = $_POST['action'] ?? '';

function valid_hex($value) {
    return preg_match('/^#[0-9A-F]{6}$/', $value);
}

if ($action === 'add') {
    $name = trim($_POST['color_name'] ?? '');
    $hex = strtoupper(trim($_POST['hex_value'] ?? ''));

    if ($name === '') {
        $errors[] = 'color name is required';
    }
    if (!valid_hex($hex)) {
        $errors[] = 'hex value must be in the format #RRGGBB';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare('INSERT INTO colors (name, hex_value) VALUES (?, ?)');
            $stmt->execute([$name, $hex]);
            $success = 'color added';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'name or hex already exists';
            } else {
                $errors[] = 'database error';
            }
        }
    }
}

if ($action === 'edit') {
    $id = (int)($_POST['edit_id'] ?? 0);
    $name = trim($_POST['edit_name'] ?? '');
    $hex = strtoupper(trim($_POST['edit_hex'] ?? ''));

    if ($id <= 0) {
        $errors[] = 'select a color to edit';
    }
    if ($name === '') {
        $errors[] = 'new color name is required';
    }
    if (!valid_hex($hex)) {
        $errors[] = 'new hex value must be in the format #RRGGBB';
    }

    if (empty($errors)) {
        $dupStmt = $pdo->prepare('SELECT COUNT(*) FROM colors WHERE (name = ? OR hex_value = ?) AND id <> ?');
        $dupStmt->execute([$name, $hex, $id]);
        $hasDup = (int)$dupStmt->fetchColumn();

        if ($hasDup > 0) {
            $errors[] = 'name or hex already exists';
        } else {
            $stmt = $pdo->prepare('UPDATE colors SET name = ?, hex_value = ? WHERE id = ?');
            $stmt->execute([$name, $hex, $id]);

            if ($stmt->rowCount() > 0) {
                $success = 'color updated';
            } else {
                $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM colors WHERE id = ?');
                $checkStmt->execute([$id]);
                if ((int)$checkStmt->fetchColumn() === 0) {
                    $errors[] = 'selected color was not found';
                } else {
                    $success = 'no change made';
                }
            }
        }
    }
}

if ($action === 'delete_confirm') {
    $id = (int)($_POST['delete_id'] ?? 0);

    $totalColors = (int)$pdo->query('SELECT COUNT(*) FROM colors')->fetchColumn();
    if ($totalColors < 2) {
        $errors[] = 'cannot delete when fewer than 2 colors exist';
    } elseif ($id <= 0) {
        $errors[] = 'select a color to delete';
    } else {
        $stmt = $pdo->prepare('DELETE FROM colors WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            $success = 'color deleted';
        } else {
            $errors[] = 'selected color was not found';
        }
    }
}

$colors = $pdo->query('SELECT id, name, hex_value FROM colors ORDER BY name ASC')->fetchAll();
$totalColors = count($colors);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Selection - Colorify</title>
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
        <li><a href="color.php">Color Coordinate</a></li>
        <li><a href="colors.php" class="active">Color Selection</a></li>
    </ul>
</nav>

<main>
    <h2 class="page-title">Color Selection</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>

    <div class="card">
        <h3 class="card-heading">Colors in Database</h3>
        <?php if (empty($colors)): ?>
            <p class="cs-empty">no colors in the database</p>
        <?php else: ?>
            <table class="cs-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Hex Value</th>
                        <th>Preview</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($colors as $c): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($c['name']); ?></td>
                            <td class="cs-hex"><?php echo htmlspecialchars($c['hex_value']); ?></td>
                            <td>
                                <span class="cs-swatch" style="background-color: <?php echo htmlspecialchars($c['hex_value']); ?>;"></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 class="card-heading">Add a Color</h3>
        <form method="post" action="colors.php" novalidate>
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label for="color_name">Color Name</label>
                    <input type="text" id="color_name" name="color_name" maxlength="100" placeholder="e.g. Magenta" value="<?php echo htmlspecialchars($_POST['color_name'] ?? ''); ?>">
                    <span class="form-hint">must be unique</span>
                </div>
                <div class="form-group">
                    <label for="hex_value">Hex Value</label>
                    <div class="hex-input-wrap">
                        <input type="color" id="hex_picker" value="<?php echo htmlspecialchars($_POST['hex_value'] ?? '#000000'); ?>" oninput="document.getElementById('hex_value').value = this.value.toUpperCase()">
                        <input type="text" id="hex_value" name="hex_value" maxlength="7" placeholder="#RRGGBB" value="<?php echo htmlspecialchars($_POST['hex_value'] ?? ''); ?>">
                    </div>
                    <span class="form-hint">must be unique and in #RRGGBB format</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add Color</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 class="card-heading">Edit a Color</h3>
        <form method="post" action="colors.php" novalidate>
            <input type="hidden" name="action" value="edit">
            <div class="form-row" style="align-items:flex-end;">
                <div class="form-group">
                    <label for="edit_id">Select Color</label>
                    <select id="edit_id" name="edit_id">
                        <option value="">choose a color</option>
                        <?php foreach ($colors as $c): ?>
                            <option
                                value="<?php echo (int)$c['id']; ?>"
                                data-name="<?php echo htmlspecialchars($c['name']); ?>"
                                data-hex="<?php echo htmlspecialchars($c['hex_value']); ?>"
                                <?php echo ((int)($_POST['edit_id'] ?? 0) === (int)$c['id']) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['hex_value']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_name">New Name</label>
                    <input type="text" id="edit_name" name="edit_name" maxlength="100" placeholder="e.g. Crimson" value="<?php echo htmlspecialchars($_POST['edit_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="edit_hex">New Hex</label>
                    <div class="hex-input-wrap">
                        <input type="color" id="edit_hex_picker" value="<?php echo htmlspecialchars($_POST['edit_hex'] ?? '#000000'); ?>" oninput="document.getElementById('edit_hex').value = this.value.toUpperCase()">
                        <input type="text" id="edit_hex" name="edit_hex" maxlength="7" placeholder="#RRGGBB" value="<?php echo htmlspecialchars($_POST['edit_hex'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 class="card-heading">Delete a Color</h3>

        <?php if ($totalColors < 2): ?>
            <p class="alert alert-info">cannot delete when fewer than 2 colors exist</p>
        <?php else: ?>
            <p style="margin-bottom:1rem; font-size:0.93rem;">select a color then click delete</p>
            <form method="post" action="colors.php" id="delete-form" novalidate>
                <input type="hidden" name="action" value="delete_confirm">
                <div class="form-row" style="align-items:flex-end;">
                    <div class="form-group">
                        <label for="delete_id">Select Color to Delete</label>
                        <select id="delete_id" name="delete_id">
                            <option value="">choose a color</option>
                            <?php foreach ($colors as $c): ?>
                                <option value="<?php echo (int)$c['id']; ?>">
                                    <?php echo htmlspecialchars($c['name']); ?> (<?php echo htmlspecialchars($c['hex_value']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger" id="delete-step1-btn">Delete</button>
                    </div>
                </div>
            </form>

            <div id="delete-confirm-panel" class="cs-confirm-panel" style="display:none;">
                <p id="delete-confirm-msg" class="cs-confirm-msg"></p>
                <div style="display:flex; gap:1rem; margin-top:0.75rem;">
                    <button type="submit" form="delete-form" class="btn btn-danger">Yes Delete</button>
                    <button type="button" class="btn btn-secondary" id="delete-cancel-btn">Cancel</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

<script>
const deleteStep1Btn = document.getElementById('delete-step1-btn');
const deleteConfirmPanel = document.getElementById('delete-confirm-panel');
const deleteConfirmMsg = document.getElementById('delete-confirm-msg');
const deleteCancelBtn = document.getElementById('delete-cancel-btn');
const deleteSelect = document.getElementById('delete_id');

if (deleteStep1Btn) {
    deleteStep1Btn.addEventListener('click', function () {
        if (!deleteSelect || deleteSelect.value === '') {
            const oldWarn = document.getElementById('delete-inline-warn');
            if (oldWarn) oldWarn.remove();
            const warn = document.createElement('p');
            warn.id = 'delete-inline-warn';
            warn.className = 'alert alert-error';
            warn.style.marginTop = '0.5rem';
            warn.textContent = 'select a color before delete';
            deleteSelect.closest('.form-row').after(warn);
            return;
        }

        const selectedText = deleteSelect.options[deleteSelect.selectedIndex].text;
        deleteConfirmMsg.textContent = 'delete ' + selectedText + ' ?';
        deleteConfirmPanel.style.display = 'block';
        deleteStep1Btn.disabled = true;
    });
}

if (deleteCancelBtn) {
    deleteCancelBtn.addEventListener('click', function () {
        deleteConfirmPanel.style.display = 'none';
        if (deleteStep1Btn) deleteStep1Btn.disabled = false;
    });
}

const addHexText = document.getElementById('hex_value');
const addHexPicker = document.getElementById('hex_picker');
if (addHexText && addHexPicker) {
    addHexText.addEventListener('input', function () {
        const val = this.value.trim();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            addHexPicker.value = val;
        }
    });
}

const editSelect = document.getElementById('edit_id');
const editName = document.getElementById('edit_name');
const editHex = document.getElementById('edit_hex');
const editHexPicker = document.getElementById('edit_hex_picker');

function fillEditFieldsFromSelect() {
    if (!editSelect) return;
    if (editName && editName.value.trim() === '' && editSelect.selectedIndex > 0) {
        editName.value = editSelect.options[editSelect.selectedIndex].dataset.name || '';
    }
    if (editHex && editHex.value.trim() === '' && editSelect.selectedIndex > 0) {
        editHex.value = (editSelect.options[editSelect.selectedIndex].dataset.hex || '').toUpperCase();
        if (editHexPicker && /^#[0-9A-Fa-f]{6}$/.test(editHex.value)) {
            editHexPicker.value = editHex.value;
        }
    }
}

if (editSelect) {
    fillEditFieldsFromSelect();
    editSelect.addEventListener('change', function () {
        const name = this.options[this.selectedIndex].dataset.name || '';
        const hex = (this.options[this.selectedIndex].dataset.hex || '').toUpperCase();
        if (editName) editName.value = name;
        if (editHex) editHex.value = hex;
        if (editHexPicker && /^#[0-9A-Fa-f]{6}$/.test(hex)) {
            editHexPicker.value = hex;
        }
    });
}

if (editHex && editHexPicker) {
    editHex.addEventListener('input', function () {
        const val = this.value.trim();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            editHexPicker.value = val;
        }
    });
}
</script>

</body>
</html>
