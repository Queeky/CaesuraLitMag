<?php 
$database = new Database(); // Fix this later
                            // Intelephense does not recognize the original $database
                            // set since nested "too deep"; doesn't actually effect
                            // function, tho
// Generating a random $_SESSION id # to help identify searches
if (!isset($_SESSION["sessionId"])) $_SESSION["sessionId"] = uniqid();

$results = $database->selectCustom("MEDIA_TYPE", ["*"]); 

if ((!isset($_SESSION["firstWorkId"])) && (!isset($_SESSION["lastWorkId"]))) {
    $table = "(SELECT WORK_ID FROM WORK ORDER BY WORK_ID ASC LIMIT 1) AS W1, "; 
    $table .= "(SELECT WORK_ID FROM WORK ORDER BY WORK_ID DESC LIMIT 1) AS W2"; 

    $idRange = $database->selectCustom($table, ["W1.WORK_ID AS FIRST_WORK", "W2.WORK_ID AS LAST_WORK"]); 

    // Finding first and last work id so archive next/previous button can sense
    // when it reaches a stopping point
    $_SESSION["firstWorkId"] = $idRange[0]["FIRST_WORK"]; 
    $_SESSION["lastWorkId"] = $idRange[0]["LAST_WORK"]; 
}

if ((!isset($_SESSION["firstSearchId"])) && (!isset($_SESSION["lastSearchId"]))) {
    $_SESSION["firstSearchId"] = $_SESSION["firstWorkId"]; 
    $_SESSION["lastSearchId"] = $_SESSION["lastWorkId"]; 
}

echo "<script>"; 
echo include("javascript/action.js"); 
echo "</script>"; 

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

    // Mobile Search Bar
    echo "<div class='mobile-search-bar'>"; 

    echo "<form action='archives.php' method='GET'>"; 
    echo "<div>";  
    echo "<input type='text' name='query' placeholder='Search works'>"; 
    echo "<button type='submit'><img src='images/SearchIcon.png'></button>"; 
    echo "</div>"; 
    echo "</form>"; 

    echo "</div>"; 
}

function displayNav($results) {
    echo "<nav>"; 

    echo "<div class='mobile-nav'>"; 
    echo "<ul>"; 
    echo "<li><a class='mobile-menu-link' href='#'>~ MENU ~</a></li>"; 

    echo "<div class='mobile-dropdown'>"; 
    echo "<ul>"; 

    echo "<li class='home-li'><a href='index.php'>HOME</a></li>";
    echo "<li class='about-li'><a href='about.php'>ABOUT</a></li>"; 
    echo "<li class='archives-li'><a href='archives.php'>ARCHIVES</a></li>";
    echo "<li class='contacts-li'><a href='contacts.php'>CONTACTS</a></li>";  
    echo "<li class='submissions-li'><a href='submissions.php'>SUBMISSIONS</a></li>";  

    echo "</ul>"; 
    echo "</div>"; 

    echo "</ul>"; 
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