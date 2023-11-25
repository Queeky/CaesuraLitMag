<?php
    session_start(); 
    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    if (isset($_POST["issAdd"])) {
        $issName = $_POST["issName"]; 
        $issDate = $_POST["issDate"]; 
        $issDescript = $_POST["issDescript"]; 
        $thumb = $_FILES["thumb"]; 
        $thumbDescript = $_POST["thumbDescript"]; 
        $thumbId = null; 

        if ($issName && $issDate && $issDescript && $thumb && $thumbDescript) { 
            // Getting just the file name (no extension)
            $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

            // Uploading file to images/
            $uploadImg = $fileSystem->upload($database, $thumb, "images"); 

            if ($uploadImg) {
                // Creating thumbnail item in database
                $newThumb = $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $thumb["name"], $thumbDescript]); 

                // Getting id of new thumbnail item
                $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 

                foreach ($ids as $id) {
                    $thumbId = $id["THUMB_ID"]; 
                }

                $added = $database->insertValues("ISSUE", ["ISS_NAME", "ISS_DATE", "THUMB_ID", "ISS_DESCRIPT"], [$issName, $issDate, $thumbId, $issDescript]); 

                if ($added) {
                    echo "<p class='header-notif'>$issName successfully added.</p>";
                } else {
                    echo "<p class='header-notif'>Error pushing $issName to database.</p>"; 
                }
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
        <title>ADD ISSUE //</title>
    </head>
    <body>
        <?php 
            $title = "ADD ISSUE"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="issueAdd.php" method='POST' enctype="multipart/form-data">
                        <label for="issName">ISSUE NAME: </label>
                        <input type="text" name='issName' placeholder='Enter issue title'>

                        <label for="issDate">ISSUE DATE: </label>
                        <input type="date" name='issDate' placeholder=''>
                        <label for="issDescript">ISSUE DESCRIPTION: </label>
                        <input type="text" name='issDescript' placeholder='Enter description'>

                        <label for="thumb">UPLOAD THUMBNAIL: </label>
                        <input type="file" name='thumb'>

                        <label for="thumbDescript">THUMBNAIL DESCRIPTION: </label>
                        <input type="text" name='thumbDescript' placeholder='Enter simple description of the thumbnail'>


                        <button type='submit' name='issAdd'>Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>