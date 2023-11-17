<?php
    session_start(); 
    include("includes/connection.inc.php");
    include("includes/files.inc.php");  

    if (isset($_POST["workRemove"])) {
        $workId = $_POST["workRemove"];
        $workName = null; 
        $workLink = null; 
        $thumbLink = null; 
        $thumbId = null; 
        $results = $database->selectCustom("WORK", ["WORK.WORK_NAME", "WORK.WORK_LINK", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["WORK_ID"], [$workId], ["="], "AND", ["THUMBNAIL"], ["WORK.THUMB_ID"], ["THUMBNAIL.THUMB_ID"]); 

        foreach ($results as $item) {
            $workName = $name["WORK_NAME"]; 
            $workLink = $name["WORK_LINK"]; 
            $thumbLink = $name["THUMB_LINK"]; 
            $thumbId = $name["THUMB_ID"]; 
        }

        // Removing thumbnail from images/ and doc from docs/
        $deleteImg = $fileSystem->delete($thumbLink, "images"); 
        $deleteDoc = $fileSystem->delete($workLink, "docs"); 

        if ($deleteImg && $deleteDoc) {
            // Put some kind of safety precautions
            $removed = $database->deleteValues("WORK", "WORK_ID", $workId); 

            if ($removed) {
                echo "<p class='header-notif'>$workName successfully removed.</p>";
            } else {
                echo "<p class='header-notif'>Error removing $workName from database.</p>"; 
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>REMOVE WORKS //</title>
    </head>
    <body>
        <?php 
            $title = "REMOVE WORKS"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="workRemove.php" method='POST'>
                        <label for="">WORKS: </label>
                        <ul>
                            <?php
                                $works = $database->selectCustom("WORK", ["WORK_ID", "WORK_NAME"]); 

                                foreach($works as $work) {
                                    echo "<li>$work[WORK_NAME]</li><button type='submit' name='workRemove' value='$work[WORK_ID]'>Remove</button>"; 
                                }
                            ?>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>