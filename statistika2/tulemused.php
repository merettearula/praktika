<?php
session_start(); // Start the session at the beginning

if (!isset($_SESSION["kasutaja_id"])) {
  // Redirect to the login page if user is not logged in
  header("Location: logi_sisse.php");
  exit();
}

if (isset($_GET["logout"])) {
  // Destroy the session.
  session_unset();
  session_destroy();
  header("Location: logi_sisse.php");
  exit();
}

// Pagination configuration
$resultsPerPage = 15; // Number of results to display per page
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number

require_once "../../config.php";
$conn = new mysqli($server_host, $server_user_name, $server_password, $dbname);
$stmt = $conn->prepare("SELECT s.id, s.kuupaev, s.oigesti, s.valesti, s.score, s.viimane_kysimus, s.oigesti_numbrid, s.valesti_numbrid, k.nimi, k.haridus, k.amet, k.huvid FROM skoorid s INNER JOIN kasutaja k ON s.kasutaja_id = k.id ORDER BY s.id desc");
$conn->set_charset("utf8");
echo $conn->error;
$stmt->execute();
$stmt->bind_result($id, $kuupaev, $oigesti, $valesti, $score, $viimane_kysimus, $oigesti_numbrid, $valesti_numbrid, $nimi, $haridus, $amet, $huvid);

$results = array();

while ($stmt->fetch()) {
    $row = array(
      "kuupaev" => $kuupaev,
      "oigesti" => $oigesti,
      "valesti" => $valesti,
      "loppskoor" => $score,
      "nimi" => $nimi,
      "haridus" => $haridus,
      "amet" => $amet,
      "huvid" => $huvid, 
      "viimane_kysimus" => $viimane_kysimus,
      "oigesti_numbrid" => $oigesti_numbrid,
      "valesti_numbrid" => $valesti_numbrid
    );
    $results[] = $row;
  }

$stmt->close();
$conn->close();

$totalResults = count($results);
$totalPages = ceil($totalResults / $resultsPerPage);

// Calculate the starting and ending indexes of the results to display for the current page
$startIndex = ($currentPage - 1) * $resultsPerPage;
$endIndex = min($startIndex + $resultsPerPage - 1, $totalResults - 1);

// Extract the results for the current page
$currentPageResults = array_slice($results, $startIndex, $resultsPerPage);

// Sorting function for multidimensional array
function multiSort($array, $sortColumn, $sortOrder) {
  $sortOrder = ($sortOrder === 'desc') ? SORT_ASC : SORT_DESC;

  usort($array, function($a, $b) use ($sortColumn, $sortOrder) {
    $valueA = $a[$sortColumn];
    $valueB = $b[$sortColumn];

    return $sortOrder === SORT_ASC ? $valueA <=> $valueB : $valueB <=> $valueA;
  });

  return $array;
}

// Sort the results array
if (isset($_GET['sortColumn']) && isset($_GET['sortOrder'])) {
  $sortColumn = $_GET['sortColumn'];
  $sortOrder = $_GET['sortOrder'];
  $currentPageResults = multiSort($currentPageResults, $sortColumn, $sortOrder);
}

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Tulemuste tabel</title>
  <link rel="stylesheet" type="text/css" href="style_stat.css">
  <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body>
  <h1>Tulemuste tabel</h1>

  <div id="filter">
    <label for="from">Alates:</label>
    <input type="date" id="from">
    <label for="to">Kuni:</label>
    <input type="date" id="to">
    <button class="filterButton" onclick="filterResults()">Filtreeri</button>
    <button class="filterButton" onclick="clearFilter()">Tühjenda</button>
    <button id="csvButton" onclick="exportToCSV()">Ekspordi CSV failina</button>
    <button id="excelButton" onclick="exportToExcel()">Ekspordi Excel failina</button>
    <button class="button clicked" id="statisticsButton" onclick="switchTable('statistics')">Tulemused</button>
    <button class="button" id="feedbackButton" onclick="switchTable('feedback')">Tagasiside</button>
    <button id="logoutButton" onclick="logout()">Logi välja</button>
  </div>

  <table id="resultsTable">
    <thead>
      <tr>
        <th data-column="0">
          Kuupäev
          <span class="clickable asc-icon" onclick="sortTable(0, 'asc')">&#x25B2;</span>
          <span class="clickable desc-icon" onclick="sortTable(0, 'desc')">&#x25BC;</span>
        </th>
        <th data-column="5">Nimi
          <span class="clickable asc-icon" onclick="sortTable(5, 'asc')">&#x25B2;</span>
          <span class="clickable desc-icon" onclick="sortTable(5, 'desc')">&#x25BC;</span>
        </th>
        <th data-column="2">Õiged vastused
          <span class="clickable asc-icon" onclick="sortTable(2, 'asc')">&#x25B2;</span>
          <span class="clickable desc-icon" onclick="sortTable(2, 'desc')">&#x25BC;</span>
        </th>
        <th data-column="3">Valed vastused
          <span class="clickable asc-icon" onclick="sortTable(3, 'asc')">&#x25B2;</span>
          <span class="clickable desc-icon" onclick="sortTable(3, 'desc')">&#x25BC;</span>
        </th>
        <th data-column="4">Lõppskoor
          <span class="clickable asc-icon" onclick="sortTable(4, 'asc')">&#x25B2;</span>
          <span class="clickable desc-icon" onclick="sortTable(4, 'desc')">&#x25BC;</span>
        </th>
       
      </tr>
    </thead>
    <tbody>
    <?php
    foreach ($currentPageResults as $result) {
        echo "<tr>";
        echo "<td>{$result['kuupaev']}</td>";
        echo "<td>{$result['nimi']}</td>";
        echo "<td>{$result['oigesti']}</td>";
        echo "<td>{$result['valesti']}</td>";
        echo "<td>{$result['loppskoor']}</td>";
        echo "<td><button class='vaata-veel-btn' onclick='loadDetails(this)'>Vaata veel</button></td>";
        echo "</tr>";
        echo "<tr class='details-row' style='display: none;'>";
        echo "<td colspan='8'>";
        echo "<strong>Haridus:</strong> {$result['haridus']}<br>";
        echo "<strong>Amet:</strong> {$result['amet']}<br>";
        echo "<strong>Huvid:</strong> {$result['huvid']}<br>";
        echo "<br><strong>Õiged vastused:</strong> {$result['oigesti']}<br>";
        echo "<strong>Õiged vastatud küsimused:</strong> {$result['oigesti_numbrid']}<br>";
        echo "<br><strong>Valesti vastatud:</strong> {$result['valesti']}<br>";
        echo "<strong>Valesti vastatud küsimused:</strong> {$result['valesti_numbrid']}<br>";
        echo "<br><strong>Viimane küsimus:</strong> {$result['viimane_kysimus']}<br>";
        echo "</td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div id="pagination">
  <?php if ($totalPages > 1): ?>
    <button class="pagination-button" onclick="changePage(<?php echo max($currentPage - 1, 1); ?>)" <?php echo $currentPage === 1 ? 'disabled' : ''; ?>>Eelmine</button>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <button class="pagination-button <?php echo $i === $currentPage ? 'active' : ''; ?>" onclick="changePage(<?php echo $i; ?>)"><?php echo $i; ?></button>
    <?php endfor; ?>
    <button class="pagination-button" onclick="changePage(<?php echo min($currentPage + 1, $totalPages); ?>)" <?php echo $currentPage === $totalPages ? 'disabled' : ''; ?>>Järgmine</button>
  <?php endif; ?>
  </div>

  

  <script src="script_tulemused.js"></script>
</body>
</html>