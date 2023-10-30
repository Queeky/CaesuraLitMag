<?php
    function displaySidebar() {
        echo "<div class='sidebar'>"; 

        echo "<form action='archives.php' method='GET' target='_blank'>"; 
        echo "<label for='query'>SEARCH WORKS</label>";
        echo "<div>";  
        echo "<input type='text' name='query' placeholder='e.g. Jane Smith poetry 2020'>"; 
        echo "<button type='submit'><img src='images/SearchIcon.png'></button>"; 
        echo "</div>"; 
        echo "</form>"; 

        echo "<div class='contact'>"; 
        echo "<label for='#'>CONTACT</label>"; 
        echo "<a href='tel: 7656772712'>(765) 677 2712</a>"; 
        echo "<a href='mailto: caesura@indwes.edu'>caesura@indwes.edu</a>"; 
        echo "<a href='mailto: eng.office@indwes.edu'>eng.office@indwes.edu</a>"; 
        echo "</div>"; 

        echo "<div class='issues'>"; 
        echo "<label for='#'>PAST ISSUES</label>"; 
        echo "<ul>"; 
        echo "<li>2023 | Name</li>"; 
        echo "<li>2023 | Name</li>"; 
        echo "<li>2023 | Name</li>"; 
        echo "<li>2023 | Name</li>";
        echo "</ul>"; 
        echo "</div>"; 

        echo "</div>"; 
    }

    displaySidebar(); 
?>