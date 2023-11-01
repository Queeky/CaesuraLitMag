<?php
    session_start(); 
    include("includes/connection.inc.php"); 

    if (isset($_POST["mediaRemove"])) {
        $mediaId = $_POST["mediaRemove"];
        $mediaName = null; 
        $names = $database->selectCustom("MEDIA_TYPE", ["MEDIA_NAME"], ["MEDIA_ID"], [$mediaId], ["="]); 

        foreach ($names as $name) {
            $mediaName = $name["MEDIA_NAME"]; 
        }

        // Put some kind of safety precautions
        $removed = $database->deleteValues("MEDIA_TYPE", "MEDIA_ID", $mediaId); 

        if ($removed) {
            echo "<p class='header-notif'>$mediaName successfully removed.</p>";
        } else {
            echo "<p class='header-notif'>Error removing $mediaName from database.</p>"; 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>REMOVE MEDIA //</title>
    </head>
    <body>
        <?php 
            $title = "REMOVE MEDIA"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="mediaRemove.php" method='POST'>
                        <label for="">MEDIA: </label>
                        <ul>
                            <?php
                                $media = $database->selectCustom("MEDIA_TYPE", ["*"]); 

                                foreach($media as $medium) {
                                    echo "<li>$medium[MEDIA_NAME]</li><button type='submit' name='mediaRemove' value='$medium[MEDIA_ID]'>Remove</button>"; 
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