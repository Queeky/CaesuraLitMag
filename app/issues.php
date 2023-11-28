<?php 
    session_start(); 

    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    function readAction($database, $fileSystem) {
        if (isset($_POST["add"])) {
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
    
                        // $msg = "A new issue has been added to Caesura Magazine!\n\n
                        //         Check it out by visiting caesuralitmag.com"; 
                        // $msg = wordwrap($msg, 70); 
    
                        // $emails = $database->selectCustom("EMAIL", ["*"]); 
    
                        // foreach ($emails as $email) {
                        //     mail($email["EMAIL_ADDRESS"], "NEW ISSUE: " . strtoupper($name), $msg); 
                        // }
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

                    $updated = $database->updateValues("ISSUE", ["THUMB_ID"], [$thumbId], ["ISS_ID"], [$id], "AND"); 

                    if ($updated) {
                        echo "<p class='header-notif'>Successfully updated.</p>";
                    } else {
                        echo "<p class='header-notif'>Error with update.</p>"; 
                    }

                    $used = $database->checkUsed($oldId); 

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
            readAction($database, $fileSystem); 

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