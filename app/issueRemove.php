<?php
    session_start(); 
    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    if (isset($_POST["issRemove"])) {
        $issId = $_POST["issRemove"];
        $issName = null;
        $thumbLink = null;  
        $thumbId = null; 
        $names = $database->selectCustom("ISSUE", ["ISSUE.ISS_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["ISS_ID"], [$issId], ["="], "AND", ["THUMBNAIL"], ["ISSUE.THUMB_ID"], ["THUMBNAIL.THUMB_ID"]); 

        foreach ($names as $name) {
            $issName = $name["ISS_NAME"]; 
            $thumbLink = $name["THUMB_LINK"]; 
            $thumbId = $name["THUMB_ID"]; 
        }

        

        // Removing thumbnail from images/
        $deleteImg = $fileSystem->delete($database, $thumbLink, "images"); 

        if ($deleteImg) {
            $removed = $database->deleteValues("WORK", "ISS_ID", $issId);  
            $removed = $database->deleteValues("ISSUE", "ISS_ID", $issId); 
            $removed = $database->deleteValues("THUMBNAIL", "THUMB_ID", $thumbId);

            if ($removed) {
                echo "<p class='header-notif'>$issName successfully removed.</p>";
            } else {
                echo "<p class='header-notif'>Error removing $issName from database.</p>"; 
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
        <title>REMOVE ISSUES //</title>
    </head>
    <body>
        <?php 
            $title = "REMOVE ISSUES"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="issueRemove.php" method='POST'>
                        <label for="">ISSUES: </label>
                        <ul>
                            <?php
                                $issues = $database->selectCustom("ISSUE", ["ISS_ID", "ISS_NAME"]); 

                                foreach($issues as $issue) {
                                    echo "<li>$issue[ISS_NAME]</li><button type='submit' name='issRemove' value='$issue[ISS_ID]'>Remove</button>"; 
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