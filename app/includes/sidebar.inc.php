<?php
    function displaySidebar($database) {
        echo "<div class='sidebar'>"; 

        // Search bar
        echo "<form action='archives.php' method='GET'>"; 
        echo "<label for='query'>SEARCH WORKS</label>";
        echo "<div>";  
        echo "<input type='text' name='query' placeholder='e.g. Jane Smith poetry 2020'>"; 
        echo "<button type='submit'><img src='images/SearchIcon.png'></button>"; 
        echo "</div>"; 
        echo "</form>"; 

        // Email Signup
        echo "<form action='index.php' method='POST'>"; 
        echo "<label for='email-signup'>EMAIL SIGNUP</label>";
        echo "<div>";  
        echo "<input class='email-signup' type='text' name='email-signup' placeholder='Enter your email'>";
        echo "<button type='submit' name='email-submit'><img src='images/EmailIcon.png'></button>";   
        echo "</div>"; 
        echo "</form>"; 

        // Contacts
        echo "<div class='contact'>"; 
        echo "<label for='#'>CONTACT</label>"; 
        echo "<a href='tel: 7656772712'>(765) 677 2712</a>"; 
        echo "<a href='mailto: caesura@indwes.edu'>caesura@indwes.edu</a>"; 
        echo "<a href='mailto: eng.office@indwes.edu'>eng.office@indwes.edu</a>"; 
        echo "</div>"; 

        // Past Issues
        echo "<div class='issues'>"; 
        echo "<label for='#'>PAST ISSUES</label>"; 
        echo "<ul>"; 

        $pastIssues = $database->selectCustom("ISSUE", ["YEAR(ISS_DATE) AS ISS_DATE", "ISS_ID", "LEFT(ISS_NAME, 15) AS ISS_NAME"], order: "ISS_DATE DESC"); 

        if ($pastIssues) {
            foreach ($pastIssues as $issue) {
                $issue["ISS_NAME"] = strtoupper($issue["ISS_NAME"]); 
                echo "<a href='archives.php?issue=$issue[ISS_ID]'><li>$issue[ISS_DATE] | $issue[ISS_NAME]</li></a>"; 
            }
        } else {
            echo "<div class='empty-message small'>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>"; 
        }

        echo "</ul>"; 
        echo "</div>"; 

        echo "</div>"; 
    }

    displaySidebar($database); 
?>