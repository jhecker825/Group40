<?php
/**
 * colors.php — Color Selection page (Milestone 2, Step 3)
 * Status: WORK IN PROGRESS
 *   ✓ Color list from DB
 *   ✓ Add a color
 *   ~ Delete a color (UI complete, deletion logic TODO)
 *   ✗ Edit a color (not yet started)
 */

require 'db.php';

$errors   = [];
$success  = '';
$action   = $_POST['action'] ?? '';

// ── ADD COLOR ────────────────────────────────────────────────────────────────
if ($action === 'add') {
    $newName = trim($_POST['color_name'] ?? '');
    $newHex  = strtoupper(trim($_POST['hex_value'] ?? ''));

    if ($newName === '') {
        $errors[] = 'Color name is required.';
    }
    if (!preg_match('/^#[0-9A-F]{6}$/', $newHex)) {
        $errors[] = 'Hex value must be in the format #RRGGBB (e.g. #FF5733).';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO colors (name, hex_value) VALUES (?, ?)");
            $stmt->execute([$newName, $newHex]);
            $success = "Color &ldquo;{$newName}&rdquo; was added successfully.";
        } catch (PDOException $e) {
            // Duplicate entry (name or hex) triggers a unique-key violation
            if ($e->getCode() === '23000') {
                $errors[] = 'A color with that name or hex value already exists. Please choose a unique name and hex value.';
            } else {
                $errors[] = 'An unexpected database error occurred. Please try again.';
            }
        }
    }
}

// ── DELETE COLOR (confirm step) ───────────────────────────────────────────────
if ($action === 'delete_confirm') {
    $deleteId = (int)($_POST['delete_id'] ?? 0);

    // Check we still have more than 2 colors before allowing deletion
    $totalColors = (int)$pdo->query("SELECT COUNT(*) FROM colors")->fetchColumn();
    if ($totalColors <= 2) {
        $errors[] = 'You cannot delete a color when there are 2 or fewer colors in the database.';
    } elseif ($deleteId <= 0) {
        $errors[] = 'No color was selected for deletion.';
    } else {
        // TODO: execute the actual DELETE query
        // $stmt = $pdo->prepare("DELETE FROM colors WHERE id = ?");
        // $stmt->execute([$deleteId]);
        // $success = 'Color deleted successfully.';

        // Placeholder until deletion logic is wired in
        $errors[] = 'Delete functionality is coming soon — the confirmation step is not yet connected.';
    }
}

// ── LOAD COLORS FROM DB ───────────────────────────────────────────────────────
$colors = $pdo->query("SELECT id, name, hex_value FROM colors ORDER BY name ASC")->fetchAll();
$totalColors = count($colors);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Color Selection — Colorify</title>
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
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo $err; ?></div>
    <?php endforeach; ?>

    <!-- ── COLOR LIST ─────────────────────────────────────────────────── -->
    <div class="card">
        <h3 class="card-heading">Colors in Database</h3>
        <?php if (empty($colors)): ?>
            <p class="cs-empty">No colors found in the database.</p>
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

    <!-- ── ADD COLOR ──────────────────────────────────────────────────── -->
    <div class="card">
        <h3 class="card-heading">Add a Color</h3>
        <form method="post" action="colors.php" novalidate>
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label for="color_name">Color Name</label>
                    <input type="text" id="color_name" name="color_name" maxlength="100"
                           placeholder="e.g. Magenta"
                           value="<?php echo htmlspecialchars($_POST['color_name'] ?? ''); ?>">
                    <span class="form-hint">Must be unique</span>
                </div>
                <div class="form-group">
                    <label for="hex_value">Hex Value</label>
                    <div class="hex-input-wrap">
                        <input type="color" id="hex_picker" name="hex_picker"
                               value="<?php echo htmlspecialchars($_POST['hex_value'] ?? '#000000'); ?>"
                               oninput="document.getElementById('hex_value').value = this.value.toUpperCase()">
                        <input type="text" id="hex_value" name="hex_value" maxlength="7"
                               placeholder="#RRGGBB"
                               value="<?php echo htmlspecialchars($_POST['hex_value'] ?? ''); ?>">
                    </div>
                    <span class="form-hint">Must be unique, format #RRGGBB</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add Color</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ── DELETE COLOR ───────────────────────────────────────────────── -->
    <div class="card">
        <h3 class="card-heading">Delete a Color</h3>

        <?php if ($totalColors <= 2): ?>
            <p class="alert alert-info">
                Deletion is disabled — the database must contain more than 2 colors before any can be removed.
            </p>
        <?php else: ?>
            <p style="margin-bottom:1rem; font-size:0.93rem;">
                Select a color below, then click <strong>Delete</strong> to begin the two-step confirmation.
            </p>
            <form method="post" action="colors.php" id="delete-form" novalidate>
                <input type="hidden" name="action" value="delete_confirm">
                <div class="form-row" style="align-items:flex-end;">
                    <div class="form-group">
                        <label for="delete_id">Select Color to Delete</label>
                        <select id="delete_id" name="delete_id">
                            <option value="">— choose a color —</option>
                            <?php foreach ($colors as $c): ?>
                                <option value="<?php echo (int)$c['id']; ?>">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                    (<?php echo htmlspecialchars($c['hex_value']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger" id="delete-step1-btn">Delete</button>
                    </div>
                </div>
            </form>

            <!-- Confirmation panel (hidden until Step 1) -->
            <div id="delete-confirm-panel" class="cs-confirm-panel" style="display:none;">
                <p id="delete-confirm-msg" class="cs-confirm-msg"></p>
                <div style="display:flex; gap:1rem; margin-top:0.75rem;">
                    <button type="submit" form="delete-form" class="btn btn-danger">
                        Yes, delete it
                    </button>
                    <button type="button" class="btn btn-secondary" id="delete-cancel-btn">
                        Cancel
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- ── EDIT COLOR (coming soon) ───────────────────────────────────── -->
    <div class="card cs-wip">
        <h3 class="card-heading">Edit a Color <span class="wip-badge">In Progress</span></h3>
        <p style="color:#78350F; font-size:0.93rem;">
            The edit interface is not yet wired up. The form below is a placeholder — 
            selecting a color will eventually pre-fill the fields, and submitting will 
            update the database.
        </p>
        <div class="form-row" style="margin-top:1rem; opacity:0.55; pointer-events:none;">
            <div class="form-group">
                <label for="edit_id">Select Color to Edit</label>
                <select id="edit_id" name="edit_id" disabled>
                    <option value="">— choose a color —</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_name">New Name</label>
                <input type="text" id="edit_name" name="edit_name" placeholder="e.g. Crimson" disabled>
            </div>
            <div class="form-group">
                <label for="edit_hex">New Hex Value</label>
                <input type="text" id="edit_hex" name="edit_hex" placeholder="#RRGGBB" disabled>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary" disabled>Save Changes</button>
            </div>
        </div>
    </div>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Colorify &mdash; Group 40</p>
</footer>

<script>
// ── Delete two-step confirmation ──────────────────────────────────────────────
const deleteStep1Btn    = document.getElementById('delete-step1-btn');
const deleteConfirmPanel = document.getElementById('delete-confirm-panel');
const deleteConfirmMsg  = document.getElementById('delete-confirm-msg');
const deleteCancelBtn   = document.getElementById('delete-cancel-btn');
const deleteSelect      = document.getElementById('delete_id');

if (deleteStep1Btn) {
    deleteStep1Btn.addEventListener('click', function () {
        const sel = deleteSelect;
        if (!sel || sel.value === '') {
            // Show inline message instead of alert
            const warn = document.createElement('p');
            warn.className = 'alert alert-error';
            warn.style.marginTop = '0.5rem';
            warn.textContent = 'Please select a color before clicking Delete.';
            const existing = document.getElementById('delete-inline-warn');
            if (existing) existing.remove();
            warn.id = 'delete-inline-warn';
            sel.closest('.form-row').after(warn);
            return;
        }
        const colorText = sel.options[sel.selectedIndex].text;
        deleteConfirmMsg.textContent =
            'Are you sure you want to permanently delete "' + colorText + '"? This cannot be undone.';
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

// Keep the color picker and hex text field in sync (text → picker)
const hexText   = document.getElementById('hex_value');
const hexPicker = document.getElementById('hex_picker');

if (hexText && hexPicker) {
    hexText.addEventListener('input', function () {
        const val = this.value.trim();
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            hexPicker.value = val;
        }
    });
}
</script>

</body>
</html>
