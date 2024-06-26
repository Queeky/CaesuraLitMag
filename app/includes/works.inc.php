<?php 
    $queryArray = explode(" ", $query); // Breaking up keywords w/ " " delimiter

    // Use switch statement to choose between searchKeys
    switch($searchKey) {
        case "query": 
            $works = $database->selectSearch($queryArray); 
            break; 
        case "issue": 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["ISSUE.ISS_ID"], [$issue], ["="], "OR", ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"], "YEAR(ISSUE.ISS_DATE)", "DESC");
            break; 
        case "media": 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["MEDIA_ID"], [$media], ["="], "OR", ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"], "YEAR(ISSUE.ISS_DATE)", "DESC");
            break;
        default: 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], jTable: ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], jColumn1: ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], jColumn2: ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"], order: "YEAR(ISSUE.ISS_DATE)", orderType: "DESC");
            break;  
    }

    function displayWorks($works) {
        echo "<div class='grid'>"; 

        if ($works) {
            foreach ($works as $work) {
                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img loading='lazy' src='$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div class='archive-info'>"; 
                echo "<p>$work[WORK_NAME]</p>"; 
                echo "<p>$work[CON_FNAME] $work[CON_LNAME]</p>";
                echo "</div>"; 
                echo "</div>"; 
            }
        } else {
            echo "<div class='empty-message large>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }

        echo "</div>"; 
    }

    function displayAdmWorks($works, $database) {
        $issues = $database->selectAllIssues(); 
        $media = $database->selectCustom("MEDIA_TYPE", ["MEDIA_ID", "MEDIA_NAME"]); 

        echo "<div class='add-form archive'>"; 
        echo "<form action='archives.php' method='POST' enctype='multipart/form-data'>"; 
        echo "<h3>ADD NEW WORK:</h3>"; 

        echo "<div class='text-input'>"; 
        echo "<input type='text' name='title' placeholder='Enter work title ***'>";
        echo "<input type='text' name='fname' placeholder='Enter contributor first name ***'>";  
        echo "<input type='text' name='lname' placeholder='Enter contributor last name ***'>";  
        echo "</div>"; 

        echo "<div class='select-input'>"; 
        echo "<label for='issue'>CHOOSE ISSUE: ***</label>"; 
        echo "<select name='issue'>"; 

        foreach ($issues as $issue) {
            echo "<option value='$issue[ISS_ID]'>$issue[ISS_DATE] | $issue[ISS_NAME]</option>"; 
        }

        echo "</select>"; 

        echo "<label for='media'>CHOOSE MEDIA TYPE: ***</label>"; 
        echo "<select name='media'>"; 

        foreach ($media as $medium) {
            echo "<option value='$medium[MEDIA_ID]'>$medium[MEDIA_NAME]</option>";
        }

        echo "</select>"; 
        echo "</div>"; 

        echo "<div class='upload-input'>"; 
        echo "<label for='doc'>UPLOAD DOCUMENT (SKIP THIS IF UPLOADING ARTWORK): ***</label>"; 
        echo "<input type='file' name='doc'>"; 
        echo "<label for='doc'>UPLOAD THUMBNAIL (ADD IMG FILE HERE IF ARTWORK): ***</label>"; 
        echo "<input type='file' name='thumb'>";  
        echo "<input type='text' name='thumbDescript' placeholder='Enter thumbnail description ***'>";
        echo "</div>"; 

        echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

        echo "</form>"; 
        echo "</div>"; 

        echo "<div class='grid'>"; 

        if ($works) {
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
        } else {
            echo "<div class='empty-message large>"; 
            echo "<p>Nothing's here at the moment!</p>"; 
            echo "</div>";
        }

        echo "</div>"; 
    }

    if (isset($_SESSION["admName"])) {
        displayAdmWorks($works, $database); 
    } else {
        displayWorks($works); 
    }
?>