const colorOptions = ['Red', 'Orange', 'Yellow', 'Green', 'Blue', 'Purple', 'Grey', 'Brown', 'Black', 'Teal'];
const colorHexMap = {
    'Red': '#a71d31',
    'Orange': '#c85626',
    'Yellow': '#d4af37',
    'Green': '#165317',
    'Blue': '#003da1',
    'Purple': '#6a0dad',
    'Grey': '#555555',
    'Brown': '#654321',
    'Black': '#000000',
    'Teal': '#0d6a6a'
};

let previousColorSelection = [];

document.getElementById('colorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const gridSize = parseInt(document.getElementById('gridSize').value);
    const colorCount = parseInt(document.getElementById('colorCount').value);
    document.getElementById('gridSizeError').style.display = 'none';
    document.getElementById('colorCountError').style.display = 'none';
    document.getElementById('duplicateColorMessage').style.display = 'none';
    
    let hasError = false;
    if (gridSize < 1 || gridSize > 26) {
        document.getElementById('gridSizeError').style.display = 'block';
        hasError = true;
    }
    if (colorCount < 1 || colorCount > 10) {
        document.getElementById('colorCountError').style.display = 'block';
        hasError = true;
    }
    if (hasError) {
        document.getElementById('tablesSection').style.display = 'none';
        return;
    }
    
    generateColorTable(colorCount);
    generateGridTable(gridSize);
    document.getElementById('tablesSection').style.display = 'block';
    document.getElementById('printGridSize').value = gridSize;
    document.getElementById('printColorCount').value = colorCount;
});

function generateColorTable(colorCount) {
    const tableBody = document.getElementById('colorTableBody');
    tableBody.innerHTML = '';
    previousColorSelection = [];
    
    for (let i = 0; i < colorCount; i++) {
        const row = document.createElement('tr');
        const dropdownCell = document.createElement('td');
        const previewCell = document.createElement('td');
        const select = document.createElement('select');
        select.className = 'color-select';
        select.id = 'colorSelect_' + i;
        
        colorOptions.forEach((color, i) => {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            select.appendChild(option);
        });
        
        if (i < colorOptions.length) {
            select.value = colorOptions[i];
            previousColorSelection[i] = colorOptions[i];
        }
        
        select.addEventListener('change', function() {
            checkColorDuplicates(i);
            updatePrintColors();
        });
        
        const preview = document.createElement('div');
        preview.className = 'color-preview';
        preview.style.backgroundColor = colorHexMap[select.value];
        preview.dataset.colorName = select.value;
        dropdownCell.className = 'color-dropdown-cell';
        previewCell.className = 'color-preview-cell';
        dropdownCell.appendChild(select);
        previewCell.appendChild(preview);
        row.appendChild(dropdownCell);
        row.appendChild(previewCell);
        tableBody.appendChild(row);
    }
}

function checkColorDuplicates(changedIndex) {
    const selectedColorSelect = document.getElementById('colorSelect_' + changedIndex);
    const newColor = selectedColorSelect.value;
    const oldColor = previousColorSelection[changedIndex];
    let isDuplicate = false;
    
    for (let i = 0; i < previousColorSelection.length; i++) {
        if (i !== changedIndex && previousColorSelection[i] === newColor) {
            isDuplicate = true;
            break;
        }
    }
    
    if (isDuplicate) {
        selectedColorSelect.value = oldColor;
        document.getElementById('duplicateColorMessage').style.display = 'block';
        setTimeout(() => {
            document.getElementById('duplicateColorMessage').style.display = 'none';
        }, 3000);
        
        return;
    }
    
    const preview = selectedColorSelect.parentElement.parentElement.querySelector('.color-preview');
    preview.style.backgroundColor = colorHexMap[newColor];
    preview.dataset.colorName = newColor;
    previousColorSelection[changedIndex] = newColor;
}

function updatePrintColors() {
    const colors = [];
    const colorCount = parseInt(document.getElementById('colorCount').value);
    for (let i = 0; i < colorCount; i++) {
        const select = document.getElementById('colorSelect_' + i);
        colors.push(select.value);
    }
    
    document.getElementById('printColors').value = JSON.stringify(colors);
}

function generateGridTable(gridSize) {
    const tableBody = document.getElementById('gridTableBody');
    tableBody.innerHTML = '';
    const headerRow = document.createElement('tr');
    const emptyCell = document.createElement('th');
    emptyCell.className = 'grid-header-cell';
    headerRow.appendChild(emptyCell);
    
    for (let col = 0; col < gridSize; col++) {
        const cell = document.createElement('th');
        cell.className = 'grid-header-cell';
        cell.textContent = String.fromCharCode(65 + col); // A=65 in ASCII
        headerRow.appendChild(cell);
    }
    
    tableBody.appendChild(headerRow);
    
    for (let row = 1; row <= gridSize; row++) {
        const tr = document.createElement('tr');
        
        const rowHeader = document.createElement('th');
        rowHeader.className = 'grid-row-header';
        rowHeader.textContent = row;
        tr.appendChild(rowHeader);
        
        for (let col = 0; col < gridSize; col++) {
            const cell = document.createElement('td');
            cell.className = 'grid-cell';
            tr.appendChild(cell);
        }
        
        tableBody.appendChild(tr);
    }
}
