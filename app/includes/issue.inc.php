<?php 
    function displayIssue($results) {
        if ($results) {
            foreach ($results as $issue) {
                echo "<div class='issue'>"; 

                echo "<div class='issue-label'>"; 
                echo "<h1>$issue[ISS_NAME]</h1>"; 
                echo "<h3>$issue[ISS_DATE]</h3>"; 
                echo "</div>"; 

                echo "<div class='issue-image'>"; 
                echo "<a href='archives.php?issue=$issue[ISS_ID]'><img src='images/$issue[THUMB_LINK]' alt='$issue[THUMB_DESCRIPT]'></a>"; 
                echo "</div>"; 

                // Eventually make the description a link, too; 
                // Leads to where?
                echo "<div class='issue-descript'>"; 
                echo "<p>$issue[ISS_DESCRIPT]</p>"; 
                echo "</div>"; 

                echo "</div>"; 
            }
        } else {
            echo "<div class='empty-message'>"; 
            echo "<p>There is nothing here at the moment!</p>"; 
            echo "</div>"; 
        }
    }

    function displayWorks($results, $database) {
        echo "<div class='issue-works'>"; 
        echo "<ul>"; 

        $id = null; 
        foreach ($results as $issue) {
            $id = $issue["ISS_ID"]; 
        }

        $results = $database->selectCustom("WORK", ["WORK.WORK_NAME", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["WORK.ISS_ID"], [$id], ["="], "AND", ["CONTRIBUTOR"], ["WORK.CON_ID"], ["CONTRIBUTOR.CON_ID"]);

        foreach ($results as $work) {
            echo "<li>"; 
            echo "<p class='title'>$work[WORK_NAME]</p>"; 
            echo "<p class='contributor'>$work[CON_FNAME] $work[CON_LNAME]</p>"; 
            echo "</li>"; 
        }

        echo "</ul>"; 
        echo "</div>"; 
    }

?>