<?php
    session_start(); 
    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    if (isset($_POST["workAdd"])) {
        $workName = $_POST["workName"]; 
        $conFName = $_POST["conFName"]; 
        $conLName = $_POST["conLName"]; 
        $issId = $_POST["issId"]; 
        $mediaId = $_POST["mediaId"];
        $workFile = $_FILES["workFile"];  
        $thumb = $_FILES["thumb"]; 
        $thumbDescript = $_POST["thumbDescript"]; 
        $thumbId = null; 
        $conId = null;  
        $workContent = null; 

        if ($workName && $conFName && $conLName && $issId && $mediaId && $workFile && $thumb && $thumbDescript) {
            // Getting just the file name (no extension) 
            $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

            // Formatting data
            $conFName = ucfirst(strtolower($conFName));
            $conLName = ucfirst(strtolower($conLName));

            $conId = $database->checkContributor($conFName, $conLName); 

            // Uploading files to docs/ and images/
            $uploadDoc = $fileSystem->upload($workFile, "docs"); 
            $uploadImg = $fileSystem->upload($thumb, "images"); 

            if ($uploadDoc) {
                $workContent = file_get_contents("docs/$workFile[name]");  

                if ($uploadImg) {
                    // Creating thumbnail item in database
                    $newThumb = $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $thumb["name"], $thumbDescript]); 

                    // Getting id of new thumbnail item
                    $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 

                    foreach ($ids as $id) {
                        $thumbId = $id["THUMB_ID"]; 
                    }
                }
            }

            $result = $database->insertValues("WORK", ["CON_ID", "ISS_ID", "THUMB_ID", "MEDIA_ID", "WORK_NAME", "WORK_CONTENT"], [$conId, $issId, $thumbId, $mediaId, $workName, $workContent]); 

            if ($result) {
                echo "<p class='header-notif'>$workName successfully added.</p>";
            } else {
                echo "<p class='header-notif'>Error pushing $workName to database.</p>"; 
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
        <title>ADD WORK //</title>
    </head>
    <body>
        <?php 
            $title = "ADD WORK"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="workAdd.php" method='POST' enctype="multipart/form-data">
                        <label for="workName">WORK NAME: </label>
                        <input type="text" name='workName' placeholder='Enter work title'>

                        <label for="conFName">AUTHOR/ARTIST FIRST NAME: </label>
                        <input type="text" name='conFName' placeholder='Enter first name'>
                        <label for="conLName">AUTHOR/ARTIST LAST NAME: </label>
                        <input type="text" name='conLName' placeholder='Enter last name'>

                        <label for="issId">ISSUE: </label>
                        <select name="issId" id="issId">
                            <?php 
                                $issues = $database->selectAllIssues(); 
                                
                                foreach ($issues as $issue) {
                                    echo "<option value='$issue[ISS_ID]'>$issue[ISS_DATE] | $issue[ISS_NAME]</option>"; 
                                }
                            ?>
                        </select>

                        <label for="mediaId">MEDIA TYPE: </label>
                        <select name="mediaId" id="mediaId">
                            <?php 
                            $media = $database->selectCustom("MEDIA_TYPE", ["MEDIA_ID", "MEDIA_NAME"]); 

                            foreach ($media as $medium) {
                                echo "<option value='$medium[MEDIA_ID]'>$medium[MEDIA_NAME]</option>"; 
                            }
                            ?>
                        </select>

                        <label for="workFile">UPLOAD DOCUMENT: </label>
                        <input type="file" name='workFile'>

                        <label for="thumb">UPLOAD THUMBNAIL: </label>
                        <input type="file" name='thumb'>

                        <label for="thumbDescript">THUMBNAIL DESCRIPTION: </label>
                        <input type="text" name='thumbDescript' placeholder='Enter simple description of the thumbnail'>

                        <button type='submit' name='workAdd'>Submit</button>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>