<?php 

$results = $database->selectCustom("MEDIA_TYPE", ["*"]); 

function displayAdmin() {
    echo "<div class='admin-bar'>"; 

    echo "<p>Welcome back, $_SESSION[admName]</p>"; 
    echo "<div class='buttons'>"; 
    echo "<div>"; 
    echo "<button><a href='about.php'>Update About</a></button>"; 
    echo "<button><a href='contacts.php'>Update Contacts</a></button>"; 
    echo "<button><a href='submissions.php'>Update Guidelines</a></button>"; 
    echo "</div>"; 
    echo "<div>"; 
    echo "<button><a href='media.php'>Update Media</a></button>"; 
    echo "<button><a href='issues.php'>Update Issues</a></button>"; 
    echo "<button><a href='archives.php'>Update Works</a></button>"; 
    echo "</div>"; 
    echo "</div>"; 

    echo "</div>"; 
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
    echo "<li><a href='index.php'>HOME</a></li>"; 
    echo "<li><a href='about.php'>ABOUT</a></li>"; 

    // Archives link and dropdown
    echo "<li class='archives-li'>"; 
    echo "<a href='archives.php'>ARCHIVES</a>"; 
    echo "<ul class='sub-nav archives'>"; 

    echo "<li><a href='archives.php'>ALL TYPES</a></li>";

    // Dynamically adding media types to dropdown
    foreach ($results as $media) {
        echo "<li><a href='archives.php?media=$media[MEDIA_ID]'>$media[MEDIA_NAME]</a></li>";
    }

    echo "</ul>"; 
    echo "</li>"; 

    // Contacts link and dropdown
    echo "<li class='contacts-li'>"; 
    echo "<a href='contacts.php'>CONTACTS</a>";
    echo "<ul class='sub-nav contacts'>"; 
    echo "<li><a href='contacts.php'>STAFF</a></li>";
    echo "<li><a href='contributors.php'>CONTRIBUTORS</a></li>";  
    echo "</ul>"; 
    echo "</li>"; 

    // Submissions link and dropdown
    echo "<li class='submissions-li'>"; 
    echo "<a href='submissions.php'>SUBMISSIONS</a>"; 
    echo "<ul class='sub-nav submissions'>"; 
    echo "<li><a href='submissions.php'>GUIDELINES</a></li>"; 
    echo "<li><a href='issues.php'>PAST ISSUES</a></li>"; 
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

if (isset($_SESSION["admName"])) {
    displayAdmin(); 
}
displayHeader($title); 
displayNav($results); 
?>