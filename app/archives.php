<?php 
    session_start(); 
    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    $query = null; 
    $issue = null; 
    $media = null; 
    $searchKey = null; 

    // Checking existence of all search variables
    if (isset($_GET['query'])) {
        $query = $_GET['query']; 
        $searchKey = "query"; 
    } 

    if (isset($_GET['issue'])) {
        $issue = $_GET['issue']; 
        $searchKey = "issue"; 
    }

    if (isset($_GET['media'])) {
        $media = $_GET['media'];
        $searchKey = "media"; 
    }

    function readAction($database, $fileSystem) {
        if (isset($_POST["add"])) {
            $title = $_POST["title"]; 
            $fname = $_POST["fname"]; 
            $lname = $_POST["lname"]; 
            $issue = $_POST["issue"]; 
            $media = $_POST["media"]; 
            $doc = $_FILES["doc"]; 
            $thumb = $_FILES["thumb"]; 
            $thumbDescript = $_POST["thumbDescript"]; 
            $thumbId = null; 
            $conId = null; 
            $content = null; 

            if ($title && $fname && $lname && $issue && $media && $doc["name"] && $thumb["name"] && $thumbDescript) {
                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

                // Formatting data
                $fname = ucfirst(strtolower($fname));
                $lname = ucfirst(strtolower($lname));

                $conId = $database->checkContributor($fname, $lname); 

                // Uploading files to docs/ and images/
                $uploadDoc = $fileSystem->upload($doc, "docs"); 
                $uploadImg = $fileSystem->upload($thumb, "images"); 

                include("includes/readFromFile.inc.php");

                if ($uploadDoc) {
                    $fileType = strtolower(pathinfo(basename($doc["name"]), PATHINFO_EXTENSION)); // Getting file extension
                    $content = readFromFile($uploadDoc, $fileType); 
    
                    $content = nl2br($content); 
    
                    if ($uploadImg) {
                        // Creating thumbnail item in database
                        $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $uploadImg, $thumbDescript]); 
    
                        // Getting id of new thumbnail item
                        $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 
    
                        foreach ($ids as $item) {
                            $thumbId = $item["THUMB_ID"]; 
                        }
    
                        $added = $database->insertValues("WORK", ["CON_ID", "ISS_ID", "THUMB_ID", "MEDIA_ID", "WORK_NAME", "WORK_CONTENT", "WORK_LINK"], [$conId, $issue, $thumbId, $media, $title, $content, $uploadDoc]); 
    
                        if ($added) {
                            echo "<p class='header-notif'>$title successfully added.</p>";
                        } else {
                            echo "<p class='header-notif'>Error pushing $title to database.</p>"; 
                        }
                    }
                }
            } else if ($title && $fname && $lname && $issue && $media && $thumb["name"] && $thumbDescript) {
                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

                // Formatting data
                $fname = ucfirst(strtolower($fname));
                $lname = ucfirst(strtolower($lname));

                $conId = $database->checkContributor($fname, $lname); 
                $content = $thumbDescript; 

                // Uploading file to images/
                $uploadImg = $fileSystem->upload($thumb, "images");

                // Creating thumbnail item in database
                $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $uploadImg, $thumbDescript]); 
    
                // Getting id of new thumbnail item
                $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 

                foreach ($ids as $item) {
                    $thumbId = $item["THUMB_ID"]; 
                }

                $added = $database->insertValues("WORK", ["CON_ID", "ISS_ID", "THUMB_ID", "MEDIA_ID", "WORK_NAME", "WORK_CONTENT", "WORK_LINK"], [$conId, $issue, $thumbId, $media, $title, $content, $uploadImg]); 

                if ($added) {
                    echo "<p class='header-notif'>$title successfully added.</p>";
                } else {
                    echo "<p class='header-notif'>Error pushing $title to database.</p>"; 
                }
            } else if ($title && $fname && $lname && $issue && $media && $doc["name"]) {
                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

                // Formatting data
                $fname = ucfirst(strtolower($fname));
                $lname = ucfirst(strtolower($lname));

                $conId = $database->checkContributor($fname, $lname); 

                // Uploading files to docs/ 
                $uploadDoc = $fileSystem->upload($doc, "docs"); 

                include("includes/readFromFile.inc.php");

                if ($uploadDoc) {
                    $fileType = strtolower(pathinfo(basename($doc["name"]), PATHINFO_EXTENSION)); // Getting file extension
                    $content = readFromFile($uploadDoc, $fileType); 
    
                    $content = nl2br($content); 

                    $issueThumb = $database->selectCustom("ISSUE", ["THUMB_ID"], ["ISS_ID"], [$issue], ["="]); 
                    foreach ($issueThumb as $item) {
                        $thumbId = $item["THUMB_ID"]; 
                    }

                    $added = $database->insertValues("WORK", ["CON_ID", "ISS_ID", "THUMB_ID", "MEDIA_ID", "WORK_NAME", "WORK_CONTENT", "WORK_LINK"], [$conId, $issue, $thumbId, $media, $title, $content, $uploadDoc]); 

                    if ($added) {
                        echo "<p class='header-notif'>$title successfully added.</p>";
                    } else {
                        echo "<p class='header-notif'>Error pushing $title to database.</p>"; 
                    }
                }
            } else {
                echo "<p class='header-notif'>A field is missing information.</p>";
            }
        } else if (isset($_POST["remove"])) {
            $id = $_POST["remove"]; 
            $title = null; 
            $docLink = null; 
            $thumbLink = null; 
            $thumbId = null; 


            $results = $database->selectCustom("WORK", ["WORK.WORK_NAME", "WORK.WORK_LINK", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["WORK_ID"], [$id], ["="], "AND", ["THUMBNAIL"], ["WORK.THUMB_ID"], ["THUMBNAIL.THUMB_ID"]); 

            foreach ($results as $item) {
                $title = $item["WORK_NAME"]; 
                $docLink = $item["WORK_LINK"]; 
                $thumbLink = $item["THUMB_LINK"]; 
                $thumbId = $item["THUMB_ID"]; 
            }

            $_SESSION["location"] = "archives.php"; 
            $_SESSION["type"] = "work"; 

            $_SESSION["id"] = $id; 
            $_SESSION["title"] = $title; 
            $_SESSION["docLink"] = $docLink; 
            $_SESSION["thumbLink"] = $thumbLink; 
            $_SESSION["thumbId"] = $thumbId; 

            $fileSystem->confirm(); 
        } else if (isset($_POST["yes"])) {
            $used = $database->checkUsed($_SESSION["thumbId"]); 

            if (isset($_SESSION["docLink"])) {
                $fileSystem->delete($_SESSION["docLink"]); 
            }
 
            $removed = $database->deleteValues("WORK", "WORK_ID", $_SESSION["id"]); 

            if (!$used) {
                $fileSystem->delete($_SESSION["thumbLink"]);
                $database->deleteValues("THUMBNAIL", "THUMB_ID", $_SESSION["thumbId"]); 
            }

            if ($removed) {
                echo "<p class='header-notif'>$_SESSION[title] successfully removed.</p>";
            } else {
                echo "<p class='header-notif'>Error removing $_SESSION[title] from database.</p>"; 
            }
        } else if (isset($_POST["update"])) {
            $id = $_POST["update"]; 
            $thumb = $_FILES["thumb"]; 
            $descript = $_POST["descript"]; 
            $thumbId = null; 
            $oldLink = null; 
            $oldId = null; 

            if ($thumb["name"] && $descript) {
                $oldThumb = $database->selectCustom("WORK", ["THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["WORK_ID"], [$id], ["="], jTable: ["THUMBNAIL"], jColumn1: ["WORK.THUMB_ID"], jColumn2: ["THUMBNAIL.THUMB_ID"]); 

                foreach ($oldThumb as $item) {
                    $oldLink = $item["THUMB_LINK"]; 
                    $oldId = $item["THUMB_ID"]; 
                }

                // Getting just the file name (no extension) 
                $thumbName = pathinfo($thumb["name"], PATHINFO_FILENAME);

                $uploadImg = $fileSystem->upload($thumb, "images"); 

                if ($uploadImg) {
                    $added = $database->insertValues("THUMBNAIL", ["THUMB_NAME", "THUMB_LINK", "THUMB_DESCRIPT"], [$thumbName, $uploadImg, $descript]); 

                    if ($added) {
                        // Getting id of new thumbnail item
                        $ids = $database->selectCustom("THUMBNAIL", ["MAX(THUMB_ID) AS THUMB_ID"]); 

                        foreach ($ids as $item) {
                            $thumbId = $item["THUMB_ID"]; 
                        }

                        $used = $database->checkUsed($oldId); 

                        $updated = $database->updateValues("WORK", ["THUMB_ID"], [$thumbId], ["WORK_ID"], [$id], "AND"); 

                        if ($updated) {
                            echo "<p class='header-notif'>Successfully updated.</p>";
                        } else {
                            echo "<p class='header-notif'>Error with update.</p>"; 
                        }

                        // If old thumbnail is not being used, remove
                        if (!$used) {
                            // Removing img from images/
                            $fileSystem->delete($oldLink); 
                            $removed = $database->deleteValues("THUMBNAIL", "THUMB_ID", $oldId); 

                            if (!$removed) {
                                echo "<p class='header-notif'>Error removing $oldLink from database.</p>";
                            } 
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
        <title>ARCHIVES //</title>
    </head>
    <body>
        <?php 
            readAction($database, $fileSystem); 

            $title = "ARCHIVES"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap'>
                <?php 
                    include("includes/works.inc.php"); 
                ?>
            </div>
        </div>
        <?php
            include("includes/footer.inc.php"); 
        ?> 
    </body>
</html>