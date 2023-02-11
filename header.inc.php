<?php

function createHeader() {
    echo "<header>";
    echo "<div id='linkButtons'>"; 
    echo "<a href='about.php' target='_blank'>About</a>"; 
    echo "<a href='archives.php' target='_blank'>Archives</a>";
    echo "<a href='contact.php' target='_blank'>Contact</a>";
    echo "<a href='submissions.php' target='_blank'>Submissions</a>";
    echo "</div>";
    echo "</header>";
}

createHeader();

?>