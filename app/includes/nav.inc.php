<?php 

$results = $database->selectCustom("MEDIA_TYPE", ["*"]); 

function displayAdmin() {
    if (isset($_SESSION["admName"])) {
        echo "<div class='admin-bar'>"; 

        echo "<p>Welcome back, $_SESSION[admName]</p>"; 
        echo "<div class='buttons'>"; 
        echo "<div>"; 
        echo "<button><a href='#'>Add/Remove Media</a></button>"; 
        echo "<button><a href='#'>Add/Remove Issues</a></button>"; 
        echo "<button><a href='#'>Add/Remove Works</a></button>"; 
        echo "</div>"; 
        echo "</div>"; 

        echo "</div>"; 
    }
}

function displayHeader($title) {
    echo "<header>"; 
    echo "<h1>$title</h1>"; 
    echo "<img src='images/CaesuraIcon.png' alt='Caesura icon'>"; 
    echo "</header>"; 
}

function displayNav($results) {
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

    // Dynamically adding media types to dropdown
    foreach ($results as $media) {
        echo "<li><a href='archives.php' target='_blank'>$media[MEDIA_NAME]</a></li>";
    }

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

displayAdmin(); 
displayHeader($title); 
displayNav($results); 
?>