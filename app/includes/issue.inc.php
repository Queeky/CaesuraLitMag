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
                echo "<a href='archives.php?issue=$issue[ISS_ID]'><img src='$issue[THUMB_LINK]' alt='$issue[THUMB_DESCRIPT]'></a>"; 
                echo "</div>"; 

                // Eventually make the description a link, too; 
                // Leads to where?
                echo "<div class='issue-descript'>"; 
                echo "<p>$issue[ISS_DESCRIPT]</p>"; 
                echo "</div>"; 

                echo "</div>"; 
            }
        } else {
            echo "<div class='empty-message large'>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
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

    function displayAdmIssue($results) {
        echo "<div class='add-form issue'>"; 
        echo "<form action='issues.php' method='POST' enctype='multipart/form-data'>"; 
        echo "<h3>ADD NEW ISSUE:</h3>"; 

        echo "<input type='text' name='name' placeholder='Enter issue title ***'>"; 
        echo "<label for='date'>ISSUE DATE: ***</label>"; 
        echo "<input type='date' name='date' placeholder=''>"; 
        echo "<textarea name='descript' placeholder='Enter issue description'></textarea>";
        echo "<label for='thumb'>UPLOAD THUMBNAIL: ***</label>";  
        echo "<input type='file' name='thumb'>"; 
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description ***'>"; 
        echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

        echo "</form>"; 
        echo "</div>"; 

        if ($results) {
            foreach ($results as $issue) {
                echo "<div class='issue issue-form'>"; 
                echo "<form action='issues.php' method='POST' enctype='multipart/form-data'>"; 

                echo "<div class='issue-label'>"; 
                echo "<h1>$issue[ISS_NAME]</h1>"; 
                echo "<h3>$issue[ISS_DATE]</h3>"; 
                echo "</div>"; 

                echo "<div class='issue-image'>"; 
                echo "<a href='archives.php?issue=$issue[ISS_ID]'><img src='$issue[THUMB_LINK]' alt='$issue[THUMB_DESCRIPT]'></a>"; 
                echo "</div>"; 

                // Showing remove and update buttons
                echo "<div class='controls'>"; 
                echo "<textarea name='descript'>$issue[ISS_DESCRIPT]</textarea>"; 
                echo "<label for='thumb'>UPDATE THUMBNAIL: </label>"; 
                echo "<input type='file' name='thumb'>"; 
                echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description'>"; 
                echo "<button class='submit-btn' type='submit' value='$issue[ISS_ID]' name='update'>Update</button>"; 
                echo "<button class='submit-btn' type='submit' value='$issue[ISS_ID]' name='remove'>Remove</button>"; 
                echo "</div>"; 
 
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<div class='empty-message large'>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>"; 
        } 
    }


?>