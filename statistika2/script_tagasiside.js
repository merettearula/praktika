function sortTable(column, order) {
    const table = document.getElementById("resultsTable");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.getElementsByTagName("tr"));
  
    rows.sort((rowA, rowB) => {
      const cellA = rowA.getElementsByTagName("td")[column].textContent.trim();
      const cellB = rowB.getElementsByTagName("td")[column].textContent.trim();
  
      if (cellA < cellB) return order === "asc" ? -1 : 1;
      if (cellA > cellB) return order === "asc" ? 1 : -1;
      return 0;
    });
  
    rows.forEach((row) => {
      tbody.appendChild(row);
    });
  }
  
  function clearFilter() {
    const table = document.getElementById("resultsTable");
    const rows = Array.from(table.getElementsByTagName("tr"));

    rows.forEach((row) => {
      row.style.display = "";
    });

    document.getElementById("from").value = "";
    document.getElementById("to").value = "";
  }

  function exportToCSV() {
    const table = document.getElementById("resultsTable");
    const rows = Array.from(table.getElementsByTagName("tr"));
    const headers = Array.from(table.getElementsByTagName("th"));

    const csvContent = [
      headers.map((header) => header.textContent).join(","),
      ...rows.map((row) =>
        Array.from(row.getElementsByTagName("td"))
          .map((cell) => cell.textContent)
          .join(",")
      ),
    ].join("\n");

    const encodedCSVContent = encodeURIComponent(csvContent);
    const downloadLink = document.createElement("a");
    downloadLink.href = "data:text/csv;charset=utf-8," + encodedCSVContent;
    downloadLink.download = "tagasiside.csv";
    downloadLink.click();
  }

  function exportToExcel() {
    const table = document.getElementById("resultsTable");
    const workbook = XLSX.utils.table_to_book(table, { sheet: "Tagasiside" });
    XLSX.writeFile(workbook, "tagasiside.xlsx");
  }

  function switchTable(tableType) {
    const statisticsButton = document.getElementById("statisticsButton");
    const feedbackButton = document.getElementById("feedbackButton");

    if (tableType === "statistics") {
      statisticsButton.classList.add("clicked");
      feedbackButton.classList.remove("clicked");

      // Modify the URL accordingly for the statistics table
      window.location.href = "tulemused.php";
    } else if (tableType === "feedback") {
      statisticsButton.classList.remove("clicked");
      feedbackButton.classList.add("clicked");

      // Modify the URL accordingly for the feedback table
      window.location.href = "tagasiside.php";
    }
  }

  function logout() {
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        window.location.href = 'logi_sisse.php';
        // Forcing page reload on browser back
        window.onunload = function(){};
        setTimeout(function(){window.onunload = function(){};}, 0);
      }
    };
    xhr.open('GET', 'log_out.php?logout=1', true);
    xhr.send();
  }

  function updateCheckbox(checkbox, id) {
    var checked = checkbox.checked ? 1 : 0;
  
    // Create an AJAX request
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState === 4 && this.status === 200) {
        // Request was successful, do something if needed
        console.log("Checkbox updated successfully.");
      }
    };
  
    // Send the checkbox value to the PHP script
    var url = "submit_data.php?id=" + encodeURIComponent(id) + "&checked=" + encodeURIComponent(checked);
    xhttp.open("GET", url, true);
    xhttp.send();
  }