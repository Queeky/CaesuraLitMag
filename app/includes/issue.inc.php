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
                echo "<img src='images/$issue[THUMB_LINK]' alt='$issue[THUMB_DESCRIPT]'>"; 
                echo "</div>"; 

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

    displayIssue($results); 
?>