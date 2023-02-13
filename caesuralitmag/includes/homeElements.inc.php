<?php 

function displayRecentIssue() {
    echo "<div id='issueDisplayed'>"; 
    echo "<h3>2023 Issue | Touching Grass </h3>";
    echo "<a href='#'><img src='images/fakeIssue.jpg'></a>";
    echo "<span><p>Description with read more link prompt here</p></span>";
    echo "</div>";

}

function displayRecentNotifs() {
    echo "<div id='currentNotifs'>";
    echo "<h3>2023 Notifications</h3>";
    echo "</div>";

}

displayRecentIssue();
displayRecentNotifs();