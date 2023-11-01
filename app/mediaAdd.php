<?php
    session_start(); 
    include("includes/connection.inc.php"); 

    if (isset($_POST["mediaAdd"])) {
        $mediaName = $_POST["mediaName"]; 

        if ($mediaName) { 
            // Formatting data
            $mediaName = ucfirst(strtolower($mediaName));

            $added = $database->insertValues("MEDIA_TYPE", ["MEDIA_NAME"], [$mediaName]); 

            if ($added) {
                echo "<p class='header-notif'>$mediaName successfully added.</p>";
            } else {
                echo "<p class='header-notif'>Error pushing $mediaName to database.</p>"; 
            }
        } else {
            echo "<p class='header-notif'>A field is missing information.</p>"; 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>ADD MEDIA //</title>
    </head>
    <body>
        <?php 
            $title = "ADD MEDIA"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="mediaAdd.php" method='POST'>
                        <label for="mediaName">MEDIA NAME: </label>
                        <input type="text" name='mediaName' placeholder='Enter media name (e.g. Fiction)'>
                        <button type='submit' name='mediaAdd'>Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>