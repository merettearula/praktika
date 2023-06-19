var sortDirection = {
  column: '',
  order: ''
};

var rowCount = 0;


function filterResults() {
  var fromInput = document.getElementById('from');
  var toInput = document.getElementById('to');
  var fromDate = fromInput.value;
  var toDate = toInput.value;

  var table = document.getElementById('resultsTable');
  var rows = table.getElementsByTagName('tr');
  var largestDate = new Date(-8640000000000000); // Initialize with a very small date
  var rowCount = 0;

  for (var i = 1; i < rows.length; i++) {
    var row = rows[i];
    var dateCell = row.cells[0];
    var dateText = dateCell.textContent.trim().split(' ')[0];
    var date = dateText.split(' ')[0];

    if (!isNaN(date) && date >= largestDate) {
      largestDate = date;
    }

    if (fromDate && date < fromDate) {
      row.style.display = 'none';
      var vaataVeelButton = row.querySelector('.vaata-veel-btn');
      var detailsRow = row.nextElementSibling;
      if (vaataVeelButton && detailsRow) {
        vaataVeelButton.innerHTML = 'Vaata veel';
        detailsRow.style.display = 'none';
      }
    } else if (toDate && date > toDate) {
      row.style.display = 'none';
      var vaataVeelButton = row.querySelector('.vaata-veel-btn');
      var detailsRow = row.nextElementSibling;
      if (vaataVeelButton && detailsRow) {
        vaataVeelButton.innerHTML = 'Vaata veel';
        detailsRow.style.display = 'none';
      }
    } else {
      row.style.display = '';
      rowCount++;
    }
  }
}

function parseDate(dateString) {
  var parts = dateString.split('-'); 
  var day = parseInt(parts[2], 10); // Day is at index 2
  var month = parseInt(parts[1], 10); // Month is at index 1
  var year = parseInt(parts[0], 10); // Year is at index 0
  return new Date(year, month - 1, day);
}


function clearFilter() {
  window.location.href = "http://greeny.cs.tlu.ee/~arulmere/test3.php";
}
  /*
  var fromInput = document.getElementById('from');
  var toInput = document.getElementById('to');

  fromInput.value = '';
  toInput.value = '';

  fromInput.value = 'none';
  toInput.value = 'none';
  
  filterResults();
}*/



//võrldeb valitud veeru praeguse ja järgmiste ridade väärtusi, määrab sortimisjärjestuse (kasvab või kahanevalt) ja vahetab vajaduse korral ridu.
function sortTable(column, order) {
  var table = document.getElementById('resultsTable');
  var rows = table.rows;
  var switching = true;
  var shouldSwitch = false;
  var switchCount = 0;

  while (switching) {
    switching = false;
    for (var i = 1; i < rows.length - 1; i++) {
      var currentRow = rows[i];
      var nextRow = rows[i + 1];

      var currentCell = currentRow.cells[column];
      var nextCell = nextRow ? nextRow.cells[column] : null;

      var currentValue = currentCell ? currentCell.innerHTML.toLowerCase() : '';
      var nextValue = nextCell ? nextCell.innerHTML.toLowerCase() : '';

      if (order === 'asc' ? currentValue > nextValue : currentValue < nextValue) {
        shouldSwitch = true;
        break;
      }
    }

    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchCount++;
    } else {
      if (switchCount === 0 && order === 'asc') {
        order = 'desc';
        switching = true;
      }
    }
  }

  updateSortIndicator(column, order);
  attachEventListeners();

}

function updateSortIndicator(column, order) {
  var headers = document.querySelectorAll('#resultsTable th span');
  for (var i = 0; i < headers.length; i++) {
    //eemaldab eelmisest sortimisest kõik sisu
    headers[i].innerHTML = '';
  }

  var currentHeader = document.querySelectorAll('#resultsTable th')[column];
  //Kui sortimise järjekord on tõusev (järjestus === 'asc' on true), 
  //valib see praeguse headeri <span> esimese elemendi, kasutades parameetrit 'span:nth-child(1)'.

  var arrow = currentHeader.querySelector(order === 'asc' ? 'span:nth-child(1)' : 'span:nth-child(2)');

  //Kui sortimisjärjekord on kahanev (järjestus === 'asc' on väär), 
  //määrab see noole <span> elemendi sisemise HTML-i väärtuseks '&#x25BC;' (allapoole kolmnurk) 
  arrow.innerHTML = order === 'asc' ? '&#x25B2;' : '&#x25BC;';

  //Kui alla kolmnurka klõsatakse, läheb funktsioon käima
  arrow.onclick = function() {
    var currentOrder = sortDirection.order === 'asc' ? 'desc' : 'asc';
    sortDirection.order = currentOrder;
    sortTable(column, currentOrder);
  };
}

function exportToCSV() {
var table = document.getElementById('resultsTable');
var rows = table.getElementsByTagName('tr');

var csvContent = "data:text/csv;charset=utf-8,";

for (var i = 0; i < rows.length; i++) {
  var row = rows[i];
  var rowData = [];
  
  // Iterate over the cells of each row
  for (var j = 0; j < row.cells.length; j++) {
    rowData.push(row.cells[j].textContent);
  }
  
  csvContent += rowData.join(",") + "\n";
}

var encodedUri = encodeURI(csvContent);
var link = document.createElement("a");
link.setAttribute("href", encodedUri);
link.setAttribute("download", "results.csv");
document.body.appendChild(link);

link.click();

document.body.removeChild(link);
}
filterResults(); // Käivitab algse filtreerimise


function exportToExcel() {
var table = document.getElementById('resultsTable');

var workbook = XLSX.utils.book_new();

var worksheet = XLSX.utils.table_to_sheet(table);

XLSX.utils.book_append_sheet(workbook, worksheet, 'Sheet1');

var excelFile = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });

saveAsExcelFile(excelFile, 'table.xlsx');
}

function saveAsExcelFile(data, filename) {
var blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
var url = URL.createObjectURL(blob);

var a = document.createElement('a');
a.href = url;
a.download = filename;

document.body.appendChild(a);
a.click();

document.body.removeChild(a);
URL.revokeObjectURL(url);
}

//saame eraldi aasta, kuu, päeva
function formatDate(date) {
  var year = date.getFullYear();
  //kui kuu on ühekohaline, kasutame meetodit padStart() et lisada null.
  var month = (date.getMonth() + 1).toString().padStart(2, '0');
  var day = date.getDate().toString().padStart(2, '0');
  return year + '-' + month + '-' + day;
}

var vaataVeelButtons = document.getElementsByClassName('vaata-veel-btn');
for (var i = 0; i < vaataVeelButtons.length; i++) {
  vaataVeelButtons[i].addEventListener('click', loadDetails);
}

function loadDetails(button) {
  var row = button.closest('tr'); // Find the closest parent table row

  if (row) {
    var detailsRow = row.nextElementSibling;

    if (detailsRow && detailsRow.classList.contains('details-row')) {
      if (detailsRow.style.display === 'none') {
        detailsRow.style.display = 'table-row';
        button.innerHTML = 'Peida';
      } else {
        detailsRow.style.display = 'none';
        button.innerHTML = 'Vaata veel';
      }
    } else {
      console.error('Invalid details row element');
    }
  } else {
    console.error('Invalid button element or parent structure');
  }
}
// Hide all details rows initially
var detailsRows = document.getElementsByClassName('details-row');
for (var i = 0; i < detailsRows.length; i++) {
  detailsRows[i].style.display = 'none';
}

function attachEventListeners() {
  var vaataVeelButtons = document.getElementsByClassName('vaata-veel-btn');
  for (var i = 0; i < vaataVeelButtons.length; i++) {
    var button = vaataVeelButtons[i];
    button.removeEventListener('click', loadDetails); // Remove existing event listener
    button.addEventListener('click', loadDetails); // Add new event listener
  }
}


