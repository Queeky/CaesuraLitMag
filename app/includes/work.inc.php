<?php 
$id = $_GET['id']; 
$works = $database->selectCustom("WORK", ["WORK.WORK_NAME", "WORK.WORK_CONTENT", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "YEAR(ISSUE.ISS_DATE) AS ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["WORK.WORK_ID"], [$id], ["="], null, ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"]);

function displayHighlight($works) {
    foreach ($works as $work) {
        // Decoding special chars
        $content = htmlspecialchars_decode($work["WORK_CONTENT"]); 

        echo "<img class='highlight-img' src='images/$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'>"; 
        echo "<div class='highlight-info'>";
        echo "<h3>$work[WORK_NAME]</h3>";  
        echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>"; 
        echo "</div>"; 
        echo "<div class='linebreak'>"; 
        echo "<hr>"; 
        echo "</div>"; 

        echo "<div class='highlight-content'>"; 
        echo "<p>$content</p>"; 

        echo "</div>"; 
    }
}

displayHighlight($works); 
?>