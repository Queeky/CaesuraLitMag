<?php 
    session_start(); 

    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 
    include("includes/mail.inc.php"); 

    function readAction($database, $fileSystem, $mail) {
        if (isset($_POST["add"])) {
            // Establishing SMTP connection
            setupMailer($mail); 

            $name = $_POST["name"]; 
            $date = $_POST["date"]; 
            $descript = $_POST["descript"]; 
            $thumb = $_FILES["thumb"]; 
            $thumbDescript = $_POST["thumbDescript"]; 
            $thumbId = null; 
    
            $added = null; 
    
            if ($name && $date && $thumb["name"] && $thumbDescript) {
                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);
    
                // Formatting data
                $name = strtoupper($name);

                $descript = nl2br($descript); 
    
                // Uploading file images/
                $uploadImg = $fileSystem->upload($thumb, "images");
    
                if ($uploadImg) {
                    $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $uploadImg, $thumbDescript]); 
    
                    // Getting id of new thumbnail item
                    $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 
    
                    foreach ($ids as $id) {
                        $thumbId = $id["THUMB_ID"]; 
                    }
    
                    // Checking if issue description exists
                    if ($descript) {
                        $added = $database->insertValues("ISSUE", ["ISS_NAME", "ISS_DATE", "THUMB_ID", "ISS_DESCRIPT"], [$name, $date, $thumbId, $descript]); 
                    } else {
                        $added = $database->insertValues("ISSUE", ["ISS_NAME", "ISS_DATE", "THUMB_ID"], [$name, $date, $thumbId]); 
                    }
    
                    if ($added) {
                        echo "<p class='header-notif'>$name successfully added.</p>";

                        $mail->isHTML(true); 
                        $mail->Subject = "NEW ISSUE: " . strtoupper($name); 
                        $mail->Body = "A new issue -- $name -- has been added to Caesura Magazine!" . "<br />" . "Check it out by visiting caesuralitmag.com" . "<br /><br />" . "Here's a sneak peek:" . "<br />" . mb_strimwidth($descript, 0, 150, "..."); 
                        $mail->AltBody = "A new issue -- $name -- has been added to Caesura Magazine!" . "\n" . "Check it out by visiting caesuralitmag.com" . "\n\n" . "Here's a sneak peek:" . "\n" . mb_strimwidth($descript, 0, 150, "...");
    
                        $emails = $database->selectCustom("EMAIL", ["*"]); 
    
                        foreach ($emails as $email) {
                            $mail->addAddress(base64_decode($email["EMAIL_ADDRESS"])); 
                        }

                        // Sending the email notifs
                        $mail->send(); 
                    } else {
                        echo "<p class='header-notif'>Error pushing $name to database.</p>"; 
                    }
                } else {
                    echo "<p class='header-notif'>Error uploading $thumb[name].</p>";
                }
            } else {
                echo "<p class='header-notif'>A field is missing information.</p>";
            }
        } else if (isset($_POST["remove"])) {
            $id = $_POST["remove"]; 
            $name = null; 
            $thumbLink = null; 
            $thumbId = null; 
    
            $issues = $database->selectCustom("ISSUE", ["ISSUE.ISS_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["ISS_ID"], [$id], ["="], "AND", ["THUMBNAIL"], ["ISSUE.THUMB_ID"], ["THUMBNAIL.THUMB_ID"]); 
    
            foreach ($issues as $issue) {
                $name = $issue["ISS_NAME"]; 
                $thumbLink = $issue["THUMB_LINK"]; 
                $thumbId = $issue["THUMB_ID"]; 
            }

            $_SESSION["location"] = "issues.php"; 
            $_SESSION["type"] = "issue"; 

            $_SESSION["id"] = $id; 
            $_SESSION["title"] = $name; 
            $_SESSION["thumbLink"] = $thumbLink; 
            $_SESSION["thumbId"] = $thumbId; 

            $fileSystem->confirm(); 
        } else if (isset($_POST["yes"])) {
            $used = $database->checkUsed($_SESSION["thumbId"]); 

            // Deleting all works associated with issue
            $database->deleteValues("WORK", "ISS_ID", $_SESSION["id"]);  
            $removed = $database->deleteValues("ISSUE", "ISS_ID", $_SESSION["id"]); 

            if ($removed) {
                echo "<p class='header-notif'>$_SESSION[title] successfully removed.</p>";
            } else {
                echo "<p class='header-notif'>Error removing $_SESSION[title] from database.</p>"; 
            }

            if (!$used) {
                // Removing thumbnail from images/
                $fileSystem->delete($_SESSION["thumbLink"]); 
                $removed = $database->deleteValues("THUMBNAIL", "THUMB_ID", $_SESSION["thumbId"]);

                if ($removed) {
                    echo "<p class='header-notif'>$_SESSION[thumbLink] successfully removed.</p>";
                } else {
                    echo "<p class='header-notif'>Error removing $_SESSION[thumbLink] from database.</p>"; 
                }
            }
        } else if (isset($_POST["update"])) {
            $id = $_POST["update"]; 
            $descript = $_POST["descript"]; 
            $thumb = $_FILES["thumb"]; 
            $thumbDescript = $_POST["thumbDescript"]; 
            $thumbId = null; 
            $oldLink = null; 
            $oldId = null; 

            if ($descript) {
                // Transforming newline chars to break tags
                $descript = nl2br($descript);

                $descriptArray = explode("<br />", $descript); 
                $descript = ""; 
                $count = count($descriptArray); 

                // Removing extra spaces
                for ($i = 0; $i < $count; $i++) {
                    if (strlen($descriptArray[$i]) < 2) {
                        unset($descriptArray[$i]); 
                    } else {
                        $descript = $descript . $descriptArray[$i] . "<br />"; 
                    }
                }


                $updated = $database->updateValues("ISSUE", ["ISS_DESCRIPT"], [$descript], ["ISS_ID"], [$id]); 

                if ($updated) {
                    echo "<p class='header-notif'>Successfully updated.</p>";
                } else {
                    echo "<p class='header-notif'>Error with update.</p>"; 
                }
            }

            if ($thumb["name"] && $thumbDescript) {
                $oldThumb = $database->selectCustom("ISSUE", ["THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["ISS_ID"], [$id], ["="], jTable: ["THUMBNAIL"], jColumn1: ["ISSUE.THUMB_ID"], jColumn2: ["THUMBNAIL.THUMB_ID"]); 

                foreach ($oldThumb as $item) {
                    $oldLink = $item["THUMB_LINK"]; 
                    $oldId = $item["THUMB_ID"]; 
                }

                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

                $uploadImg = $fileSystem->upload($thumb, "images"); 
                $added = $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $uploadImg, $thumbDescript]); 

                if ($added) {
                    // Getting id of new thumbnail item
                    $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 

                    foreach ($ids as $item) {
                        $thumbId = $item["THUMB_ID"]; 
                    }

                    $used = $database->checkUsed($oldId); 

                    $updated = $database->updateValues("ISSUE", ["THUMB_ID"], [$thumbId], ["ISS_ID"], [$id], "AND"); 

                    if ($updated) {
                        echo "<p class='header-notif'>Successfully updated.</p>";
                    } else {
                        echo "<p class='header-notif'>Error with update.</p>"; 
                    }

                    if (!$used) {
                        // Removing img from images/
                        $deleteImg = $fileSystem->delete($oldLink); 
                        $removed = $database->deleteValues("THUMBNAIL", "THUMB_ID", $oldId); 

                        if (!$removed) {
                            echo "<p class='header-notif'>Error removing $oldLink from database.</p>";
                        } 
                    }
                }
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
        <title>ISSUES //</title>
    </head>
    <body>
        <?php 
            readAction($database, $fileSystem, $mail); 

            $title = "ISSUES"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left issue-page'>
                <?php 
                    $results = $database->selectAllIssues(); 
                    include("includes/issue.inc.php"); 

                    if (isset($_SESSION["admName"])) {
                        displayAdmIssue($results); 
                    } else {
                        displayIssue($results);
                    } 
                ?>
            </div>
            <?php 
                include("includes/sidebar.inc.php"); 
            ?>
        </div>
        <?php
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>