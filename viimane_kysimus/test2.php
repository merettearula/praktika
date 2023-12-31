<?php
session_start();
echo $_SESSION['kasutaja_id'];

$csvData = file_get_contents('temp.csv');
$rows = explode("\n", $csvData);
$maxScore = 0;

// Iterate over each row (starting from index 1 to skip the header row)
for ($i = 1; $i < count($rows); $i++) {
    // Split the row into columns
    $columns = explode(";", $rows[$i]);
    
    $largestScore = 0;
    
    for ($j = 3; $j < count($columns); $j += 3) {
      $score = intval($columns[$j]);
      if ($score > $largestScore) {
        $largestScore = $score;
      }
    }
    
    $maxScore += $largestScore;
  }

echo '<script>const maxScore = ' . $maxScore . ';</script>';

?>


<!DOCTYPE html>
<html>
<head>
  <title>Intervjuu</title>
  <link rel="stylesheet" type="text/css" href="style_disain.css">
</head>
<body>
  <div class="container">
    <div id="start-section">
	<video id="intro-video" width="800" autoplay="autoplay" muted playsinline style="pointer-events: none;">
          <source src="tutvustus.mp4" type="video/mp4">
    </video>
      <button id="start-btn" >Start</button> <!--style="visibility: hidden;"-->
  </div>
    
    <div id="intro-section" style="display: none;">
    
      <button id="intro-btn" >Intro</button>

      
    </div>

    <div id="question-section" style="display: none;">

      
      <div id="video-container">
        <video id="default-video" width="800" autoplay="autoplay" muted playsinline style="pointer-events: none;">
          <source src="default_video.mp4" type="video/mp4">
        </video>
    
        <video id="video1" width="800" autoplay="autoplay" muted playsinline style="pointer-events: none;">
          <source src="video2.mp4" type="video/mp4">
        </video>
    
        <video id="video2" width="800" autoplay="autoplay" muted playsinline style="pointer-events: none;">
          <source src="video1.mp4" type="video/mp4">
        </video>

        <!--<button onclick="playVideo('video1')">Play Video 1</button>
        <button onclick="playVideo('video2')">Play Video 2</button>-->
        
      </div>



      <h2 id="question"></h2>
      <div id="answer-buttons"></div>
      
      <button id="next-btn" disabled>Next Question</button>
    </div>

    

    <div id="end-section" style="display: none;">
      <h2 id="end-title"></h2>
      <p id="end-score"></p>
    </div>
  </div>

  <script src="script.js" defer></script>
</body>
</html>