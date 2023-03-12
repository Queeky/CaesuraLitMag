<?php

function displayHeader() {
    echo "<nav>";
    echo "<div id='buttonsWrap'>"; 
    echo "<a href='about.php' target='_blank'>ABOUT</a>"; 
    echo "<a href='archives.php' target='_blank'>ARCHIVES</a>";
    echo "<a href='contact.php' target='_blank'>CONTACT</a>";
    echo "<a href='submissions.php' target='_blank'>SUBMISSIONS</a>";
    echo "</div>";
    echo "</nav>";
}

displayHeader();

?>