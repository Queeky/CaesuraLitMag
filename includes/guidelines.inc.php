<?php

$sql = "SELECT * FROM submissions";

$result = mysqli_query($conn, $sql);
$listItems = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_free_result($result);
mysqli_close($conn);

function displayGuidelines($listItems) {
    echo "<div id='submissionGuidelines'>";
    echo "<h3>Literary and Art Submission Guidelines</h3>";
    echo "<ul>";

    // Loops through website_info table and display all elements of sub_guidelines

    if ($listItems != null) {
        foreach ($listItems as $item) {
            echo "<li>$item[guidelines]</li>";
        }
    }

    echo "</ul>";
    echo "</div>";
}

displayGuidelines($listItems);

?>