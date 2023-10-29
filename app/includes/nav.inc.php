<?php 

function displayHeader($title) {
    echo "<header>"; 
    echo "<h1>$title</h1>"; 
    echo "<img src='images/CaesuraIcon.png' alt='Caesura icon'>"; 
    echo "</header>"; 
}

function displayNav() {
    echo "<nav>"; 

    echo "<div class='mobile-nav'>"; 
    echo "<div class='mobile-dropdown'>"; 
    echo "</div>"; 
    echo "</div>"; 

    echo "<div class='full-nav'>"; 
    echo "<ul>"; 
    echo "<li><a href='index.php' target='_blank'>HOME</a></li>"; 
    echo "<li><a href='about.php' target='_blank'>ABOUT</a></li>"; 

    // Archives link and dropdown
    echo "<li class='archives-li'>"; 
    echo "<a href='archives.php' target='_blank'>ARCHIVES</a>"; 
    echo "<ul class='sub-nav archives'>"; 
    echo "<li><a href='archives.php' target='_blank'>ALL TYPES</a></li>"; 
    echo "<li><a href='archives.php' target='_blank'>POETRY</a></li>";
    echo "<li><a href='archives.php' target='_blank'>FICTION</a></li>";
    echo "<li><a href='archives.php' target='_blank'>NONFICTION</a></li>";
    echo "<li><a href='archives.php' target='_blank'>ART</a></li>";
    echo "</ul>"; 
    echo "</li>"; 

    // Contacts link and dropdown
    echo "<li class='contacts-li'>"; 
    echo "<a href='contacts.php' target='_blank'>CONTACTS</a>";
    echo "<ul class='sub-nav contacts'>"; 
    echo "<li><a href='contacts.php' target='_blank'>STAFF</a></li>";
    echo "<li><a href='contributors.php' target='_blank'>CONTRIBUTORS</a></li>";  
    echo "</ul>"; 
    echo "</li>"; 

    // Submissions link and dropdown
    echo "<li class='submissions-li'>"; 
    echo "<a href='submissions.php' target='_blank'>SUBMISSIONS</a>"; 
    echo "<ul class='sub-nav submissions'>"; 
    echo "<li><a href='submissions.php' target='_blank'>GUIDELINES</a></li>"; 
    echo "<li><a href='issues.php' target='_blank'>PAST ISSUES</a></li>"; 
    echo "</ul>"; 
    echo "</li>"; 

    echo "</ul>"; 

    // Color bar
    echo "<div class='color-bar'>"; 
    echo "<div class='orange'></div>"; 
    echo "<div class='pink'></div>"; 
    echo "<div class='green'></div>"; 
    echo "<div class='blue'></div>"; 
    echo "<div class='purple'></div>"; 
    echo "</div>"; 

    echo "</div>"; 

    echo "</nav>"; 
}

displayHeader($title); 
displayNav(); 
?>