<?php 
    $queryArray = explode(" ", $query); // Breaking up keywords w/ " " delimiter
    
    $idCount; 
    $mediaId;
    $issId;
    $conId;
    $workId;  
    
    $mediaKeys = array(); 
    $issKeys = array();
    $conKeys = array();
    $workKeys = array();
    
    $allIds = array(); 

    $works = null; 
    
    foreach ($queryArray as $keyword) {
        $mediaId = $database->selectCustom("MEDIA_TYPE", ["MEDIA_ID"], ["MEDIA_NAME"], [$keyword], ["="]); 
        $issId = $database->selectCustom("ISSUE", ["ISS_ID"], ["ISS_NAME", "YEAR(ISS_DATE)"], [$keyword, $keyword], ["like", "="], "OR"); 
        $conId = $database->selectCustom("CONTRIBUTOR", ["CON_ID"], ["CON_FNAME", "CON_LNAME"], [$keyword, $keyword], ["=", "="], "OR");
        $workId = $database->selectCustom("WORK", ["WORK_ID"], ["WORK_NAME", "WORK_CONTENT"], [$keyword, $keyword], ["=", "like"], "OR");
    
        // Checking if the ids exist
        // If yes, extracts and adds elements to new array
        if ($mediaId) {
            foreach ($mediaId as $id) {
                array_push($mediaKeys, $id["MEDIA_ID"]); 
            }
    
            $allIds["MEDIA_TYPE.MEDIA_ID"] = $mediaKeys; 
        } 
    
        if ($issId) {
            foreach ($issId as $id) { 
                array_push($issKeys, $id["ISS_ID"]);
            }
    
            $allIds["ISSUE.ISS_ID"] = $issKeys; 
        } 
        
        if ($conId) {
            foreach ($conId as $id) {
                array_push($conKeys, $id["CON_ID"]);
            }
    
            $allIds["CONTRIBUTOR.CON_ID"] = $conKeys;
        } 
    
        if ($workId) {
            foreach ($workId as $id) { 
                array_push($workKeys, $id["WORK_ID"]);
            }
    
            $allIds["WORK.WORK_ID"] = $workKeys;
        }
    
        // echo "<p>All Keys</p>"; 
        // var_dump($allIds);  
        // echo "<p>End of keys</p>"; 
    }

    // Use switch statement to choose between searchKeys
    switch($searchKey) {
        case "query": 
            $works = $database->selectSearch($allIds);
            break; 
        case "issue": 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["ISSUE.ISS_ID"], [$issue], ["="], "OR", ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"]);
            break; 
        case "media": 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], ["MEDIA_ID"], [$media], ["="], "OR", ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"]);
            break;
        default: 
            $works = $database->selectCustom("WORK", ["WORK.WORK_ID", "WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_DESCRIPT", "ISSUE.ISS_NAME", "ISSUE.ISS_DATE", "CONTRIBUTOR.CON_FNAME", "CONTRIBUTOR.CON_LNAME"], jTable: ["THUMBNAIL", "ISSUE", "CONTRIBUTOR"], jColumn1: ["WORK.THUMB_ID", "WORK.ISS_ID", "WORK.CON_ID"], jColumn2: ["THUMBNAIL.THUMB_ID", "ISSUE.ISS_ID", "CONTRIBUTOR.CON_ID"]);
            break;  
    }

    // var_dump(count($works)); 

    function displayWorks($works) {
        echo "<div class='grid'>"; 

        if ($works) {
            foreach ($works as $work) {
                echo "<div class='archive-item'>"; 
                echo "<a href='highlight.php?id=$work[WORK_ID]'><img src='images/$work[THUMB_LINK]' alt='$work[THUMB_DESCRIPT]'></a>"; 
                echo "<div>"; 
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

    displayWorks($works); 
?>