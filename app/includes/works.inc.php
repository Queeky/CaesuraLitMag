<?php 
    $queryArray = explode(" ", $query); // Breaking up keywords w/ " " delimiter
    $database = new Database(); // Fix this later

    $works = $database->collectWorks($searchKey, $queryArray); 

    function displayWorks($works, $query) {
        if ($works) {
            // Checking if a search is in action; otherwise, WORK_PRIORITY does 
            // not exist in results
            $query ? $priority = intval($works[0]["WORK_PRIORITY"]) : $priority = null; 

            // If default, WORK_ID; otherwise ORDER_ID
            (isset($_GET["issue"]) || isset($_GET["media"]) || isset($_GET["query"])) ? $firstShownId = $works[0]["ORDER_ID"] : $firstShownId = $works[0]["WORK_ID"]; 
            (isset($_GET["issue"]) || isset($_GET["media"]) || isset($_GET["query"])) ? $lastShownId = $works[count($works) - 1]["ORDER_ID"] : $lastShownId = $works[count($works) - 1]["WORK_ID"]; 

            // echo "<br>" . "FIRST LOADED ID: " . $firstShownId . "<br>"; 
            // echo "<br>" . "LAST LOADED ID: " . $lastShownId . "<br>";

            echo "<div class='grid'>"; 

            foreach ($works as $work) {
                if ($priority && ($priority > 1 && intval($work["WORK_PRIORITY"]) <= 1)) {
                    echo "</div>"; 

                    echo "<div class='similar-results'>"; 
                    echo "<p>Results similar to '$query'</p>"; 
                    echo "</div>"; 

                    echo "<div class='grid'>"; 
                }

                // Setting new priority
                $priority ? $priority = intval($work["WORK_PRIORITY"]) : $priority = null; 

                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img loading='lazy' src='$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div class='archive-info'>"; 
                echo "<p>$work[WORK_NAME]</p>"; 
                echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>";
                echo "</div>"; 
                echo "</div>"; 
            }

            echo "</div>"; 

            echo "<div class='next-page'>";

            if ($firstShownId == $_SESSION["lastSearchId"]) {
                echo "<p style='color:grey; text-decoration:none;'>Previous Page</p>"; 
            } else {
                echo "<a href='archives.php?$_GET[urlKeypair]fs=$firstShownId'>Previous Page</a>"; 
            }

            echo "<p class='line-break'> | </p>"; 

            if ($lastShownId == $_SESSION["firstSearchId"]) {
                echo "<p style='color:grey; text-decoration:none;'>Next Page</p>"; 
            } else {
                echo "<a href='archives.php?$_GET[urlKeypair]ls=$lastShownId'>Next Page</a>"; 
            }

            echo "</div>"; 
        } else if (!$works && $query) {
            echo "<div class='empty-message large'>"; 
            echo "<p>No results for '$query.'</p>"; 
            echo "</div>";
        } else {
            echo "<div class='empty-message large'>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }
    }

    function displayAdmWorks($works, $database) {
        $issues = $database->selectAllIssues(); 
        $media = $database->selectCustom("MEDIA_TYPE", ["MEDIA_ID", "MEDIA_NAME"]); 

        echo "<div class='add-form archive'>"; 
        echo "<form action='archives.php' method='POST' enctype='multipart/form-data'>"; 
        echo "<h3>ADD NEW WORK:</h3>"; 

        echo "<div class='text-input'>"; 
        echo "<input type='text' name='title' placeholder='Enter work title'>";
        echo "<input type='text' name='fname' placeholder='Enter contributor first name'>";  
        echo "<input type='text' name='lname' placeholder='Enter contributor last name'>";  
        echo "</div>"; 

        echo "<div class='select-input'>"; 
        echo "<label for='issue'>CHOOSE ISSUE:</label>"; 
        echo "<select name='issue'>"; 

        foreach ($issues as $issue) {
            echo "<option value='$issue[ISS_ID]'>$issue[ISS_DATE] | $issue[ISS_NAME]</option>"; 
        }

        echo "</select>"; 

        echo "<label for='media'>CHOOSE MEDIA TYPE:</label>"; 
        echo "<select name='media'>"; 

        foreach ($media as $medium) {
            echo "<option value='$medium[MEDIA_ID]'>$medium[MEDIA_NAME]</option>";
        }

        echo "</select>"; 
        echo "</div>"; 

        echo "<div class='upload-type'>"; 
        echo "<label>IS THIS WORK LITERATURE OR ART?</label>"; 
        echo "<div>"; 
        echo "<button class='submit-btn upload-type-lit' type='button' name='upload-type'>Literature</button>"; 
        echo "<button class='submit-btn upload-type-art' type='button' name='upload-type'>Art</button>";
        echo "</div>"; 
        echo "</div>"; 

        echo "<div class='upload-input'>"; 
        echo "<div id='lit'>"; 
        echo "<label for='doc'>UPLOAD DOCUMENT:</label>"; 
        echo "<input type='file' name='doc'>"; 
        echo "<label for='doc'>UPLOAD THUMBNAIL (OPTIONAL):</label>"; 
        echo "<input type='file' name='thumb'>";  
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description'>";
        echo "</div>"; 

        echo "<div id='art'>"; 
        echo "<label for='doc'>UPLOAD THUMBNAIL:</label>"; 
        echo "<input type='file' name='thumb'>";  
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description'>";
        echo "</div>"; 
    
        echo "</div>"; 

        echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

        echo "</form>"; 
        echo "</div>"; 

        if ($works) {
            echo "<div class='grid'>"; 

            foreach ($works as $work) {
                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img loading='lazy' src='$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div class='archive-info'>"; 
                echo "<p>$work[WORK_NAME]</p>"; 
                echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>";
                echo "</div>"; 

                echo "<div class='controls'>"; 
                echo "<form action='archives.php' method='POST' enctype='multipart/form-data'>"; 
                echo "<label for='thumb'>UPDATE THUMBNAIL: </label>"; 
                echo "<input type='file' name='thumb'>";
                echo "<input type='text' name='descript' placeholder='Enter thumbnail description ***'>"; 
                echo "<div>"; 
                echo "<button class='submit-btn' type='submit' name='update' value='$work[WORK_ID]'>Update</button>"; 
                echo "<button class='submit-btn' type='submit' name='remove' value='$work[WORK_ID]'>Remove</button>"; 
                echo "</div>";
                echo "</form>";  
                echo "</div>"; 
                echo "</div>"; 
            }

            echo "</div>"; 
        } else {
            echo "<div class='empty-message large>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }
    }

    if (isset($_SESSION["admName"])) {
        displayAdmWorks($works, $database); 
    } else {
        displayWorks($works, $query); 
    }
?>