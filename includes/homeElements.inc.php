<?php 

function displayRecentIssue() {
    echo "<div id='issueDisplayed'>"; 
    echo "<p>2023 Issue | Touching Grass</p>";
    echo "<a href='post.php' target='_blank'><img src='images/fakeIssue.jpg'></a>";
    echo "<span><p>Description with read more link prompt here</p></span>";
    echo "</div>";

}

function displayRecentNotifs() {
    echo "<div id='currentNotifs'>";
    echo "<p>2023 Notifications</p>";
    echo "</div>";

}

displayRecentIssue();
displayRecentNotifs();