<?php
if (isset($_GET["logout"])) {
  session_destroy();
  header("Location: ../admin/logi_sisse.php");
  exit();
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
  <h1>Statistika tabel</h1>

  <div id="filter">
    <label for="from">Alates:</label>
    <input type="date" id="from">
    <label for="to">Kuni:</label>
    <input type="date" id="to">
    <button onclick="filterResults()">Filtreeri</button>
    <button onclick="clearFilter()">Tühjenda</button>
    <button id="csvButton" onclick="exportToCSV()">Export CSV</button>
    <button id="excelButton" onclick="exportToExcel()">Export Excel</button>
    <button class="button clicked" id="statisticsButton" onclick="window.location.href='statistika.html'">Statistika</button>
    <button class="button" id="feedbackButton" onclick="window.location.href='tagasiside.html'">Tagasiside</button>
    <button id="logoutButton" onclick="logout()">Log Out</button>
    <script>
      function logout() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
          if (xhr.readyState === 4 && xhr.status === 200) {
            window.location.href = 'https://greeny.cs.tlu.ee/~arulmere/admin/logi_sisse.php';
          }
        };
        xhr.open('GET', 'logout.php?logout=1', true);
        xhr.send();
      }
    </script>
  </div>

<table id="resultsTable">
  <thead>
      <tr>
        
        <th data-column="0" data-type="date">
          Kuupäev
          <span class="clickable" onclick="sortTable(0, 'asc')">&#x25B2;</span>
          <span onclick="sortTable(0, 'desc')">&#x25BC;</span>
        </th>
        <th data-column="1">Nimi <span class="clickable" onclick="sortTable(1, 'asc')">&#x25B2;</span><span onclick="sortTable(1, 'desc')">&#x25BC;</span></th>
        <th data-column="2">Nupp <span class="clickable" onclick="sortTable(2, 'asc')">&#x25B2;</span><span onclick="sortTable(2, 'desc')">&#x25BC;</span></th>
        <th data-column="3">Tagasiside <span class="clickable" onclick="sortTable(3, 'asc')">&#x25B2;</span><span onclick="sortTable(3, 'desc')">&#x25BC;</span></th>
        <th data-column="4">Küsimus <span class="clickable" onclick="sortTable(4, 'asc')">&#x25B2;</span><span onclick="sortTable(4, 'desc')">&#x25BC;</span></th>
        <th data-column="5">Email <span class="clickable" onclick="sortTable(5, 'asc')">&#x25B2;</span><span onclick="sortTable(5, 'desc')">&#x25BC;</span></th>
      </tr>
    </thead>
    <tbody>
    <?php
    require_once "../../config.php";
    $conn = new mysqli($server_host, $server_user_name, $server_password, $dbname);
    $stmt = $conn->prepare("SELECT tagasiside.id, tagasiside.tagasiside, tagasiside.email, tagasiside.nupp, tagasiside.kysimus, tagasiside.kasutaja_id, tagasiside.lisatud_kuupaev, k.nimi FROM tagasiside tagasiside INNER JOIN kasutaja k ON tagasiside.kasutaja_id = k.id");
    $conn->set_charset("utf8");
    echo $conn->error;
    $stmt->execute();
    $stmt->bind_result($id, $tagasiside, $email, $nupp, $kysimus, $kasutaja_id, $lisatud_kuupaev, $nimi);

    while ($stmt->fetch()) {
      if($nupp == 1) {
        $nupp = "&#128077";
      } else {
        $nupp = "&#128078";
      }

      echo "<tr>";
      echo "<td class='wrap-cell'>$lisatud_kuupaev</td>";
      echo "<td class='wrap-cell'>$nimi</td>";
      echo "<td class='wrap-cell'>$nupp</td>";
      echo "<td class='wrap-cell'>$tagasiside</td>";
      echo "<td class='wrap-cell'>$kysimus</td>";
      echo "<td class='wrap-cell'>$email</td>";
      echo "</tr>";

    }

    $stmt->close();
    $conn->close();
  ?>
</tbody>
</table>

<script src="script.js"></script>
</body>
</html>

