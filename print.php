<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="print.css">
</head>
<body>
    <?php
        $gridSize = isset($_POST['gridSize']) ? intval($_POST['gridSize']) : 5;
        $colorCount = isset($_POST['colorCount']) ? intval($_POST['colorCount']) : 3;
        $colorJSON = isset($_POST['colors']) ? $_POST['colors'] : '[]';
        $colors = json_decode($colorJSON, true);
        
        if ($gridSize < 1 || $gridSize > 26) {
            $gridSize = 5;
        }
        if ($colorCount < 1 || $colorCount > 10) {
            $colorCount = 3;
        }
        
        // Color hex values (grayscale for print)
        $colorHexMap = [
            'Red' => '#4d4d4d',
            'Orange' => '#666666',
            'Yellow' => '#cccccc',
            'Green' => '#999999',
            'Blue' => '#333333',
            'Purple' => '#555555',
            'Grey' => '#aaaaaa',
            'Brown' => '#6b6b6b',
            'Black' => '#000000',
            'Teal' => '#808080'
        ];
    ?>
    
    <button class="btn-back" onclick="window.history.back()">Back to Color Coordinator</button>
    
    <div class="print-container">
        <div class="print-header">
            <div class="print-logo">[LOGO] ColorSync</div>
            <p style="margin: 0; font-size: 11px;">Color Coordinate Sheet</p>
        </div>
        
        <div class="print-content">
            <h2>Selected Colors</h2>
            <table class="color-table">
                <tbody>
                    <?php
                        for ($i = 0; $i < $colorCount; $i++) {
                            $colorName = isset($colors[$i]) ? $colors[$i] : 'Color ' . ($i + 1);
                            $colorGray = isset($colorHexMap[$colorName]) ? $colorHexMap[$colorName] : '#999999';
                            echo '<tr>';
                            echo '<td style="background-color: ' . $colorGray . '; width: 20%;"></td>';
                            echo '<td style="text-align: left; width: 80%;">' . htmlspecialchars($colorName) . '</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
            
            <h2>Coordinate Grid</h2>
            <table class="grid-table">
                <tbody>
                    <?php
                        echo '<tr>';
                        echo '<th style="width: 15px; height: 15px;"></th>';
                        for ($col = 0; $col < $gridSize; $col++) {
                            echo '<th style="width: 15px; height: 15px;">' . chr(65 + $col) . '</th>';
                        }
                        echo '</tr>';
                        
                        for ($row = 1; $row <= $gridSize; $row++) {
                            echo '<tr>';
                            echo '<th style="width: 15px; height: 15px;">' . $row . '</th>';
                            for ($col = 0; $col < $gridSize; $col++) {
                                echo '<td style="width: 15px; height: 15px;"></td>';
                            }
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
