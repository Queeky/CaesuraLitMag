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
            $thumbId = null; 
            $conId = null; 
            $content = null; 
            $error = 0; 

            // Checking submitted form's info
            if ($_POST["title"] && $_POST["fname"] && $_POST["lname"] && $_POST["issue"] && $_POST["media"]) {
                // Formatting contributor name
                $fname = ucfirst(strtolower($_POST["fname"]));
                $lname = ucfirst(strtolower($_POST["lname"]));

                // Ensuring valid submission (if uploaded img, must have descript)
                if (($_FILES["doc"]["name"] && ($_FILES["thumb"]["name"] && $_POST["thumbDescript"])) ||
                    ($_FILES["doc"]["name"] && (!$_FILES["thumb"]["name"] && !$_POST["thumbDescript"])) ||
                    ($_FILES["thumb"]["name"] && $_POST["thumbDescript"])) {

                    include("includes/readFromFile.inc.php");

                    if ($_FILES["doc"]["name"]) {
                        $content = readFromFile($_FILES["doc"]["tmp_name"]); 
                        $content = $database->sanitize([$content]); 
                        $content = nl2br($content[0]); 

                        // If thumbnail was not included, get issue thumbnail 
                        if (!$_FILES["thumb"]["name"]) {
                            $thumbId = $database->selectCustom("ISSUE", ["THUMB_ID"], ["ISS_ID"], [$_POST["issue"]], ["="])[0]["THUMB_ID"];
                        }
                    }
    
                    if ($_FILES["thumb"]["name"] && $_POST["thumbDescript"]) {
                        // Uploading file to images/
                        $uploadImg = $fileSystem->upload($_FILES["thumb"], "images"); 

                        // If doc was not included, make thumbnail descript the content
                        if (!$_FILES["doc"]["name"]) {
                            $content = $database->sanitize([$_POST["thumbDescript"]])[0]; 
                        }

                        if ($uploadImg) {
                            $thumbId = $database->insertReturnId($uploadImg, $_POST["thumbDescript"], "thumb"); 
                        } else {
                            echo "<p class='header-notif'>Failed to upload file \"$_FILES[thumb][name]\"</p>"; 
    
                            $error = 1; 
                        }
                    }   

                    if ($error == 0) {
                        $conId = $database->insertReturnId($fname, $lname, "con"); 

                        $added = $database->insertValues("WORK", ["CON_ID", "ISS_ID", "THUMB_ID", "MEDIA_ID", "WORK_NAME", "WORK_CONTENT"], [$conId, $_POST["issue"], $thumbId, $_POST["media"], $_POST["title"], $content]);

                        echo ($added) ? "<p class='header-notif'>Successfully added $_POST[title].</p>" : ""; 
                    } else {
                        echo "<p class='header-notif'>Unable to push $_POST[title] to database.</p>";
                    } 
                } else {
                    echo "<p class='header-notif'>A field is missing information.</p>";
                }
            } else {
                echo "<p class='header-notif'>A field is missing information.</p>";
            }
        } else if (isset($_POST["remove"])) {
            // $_POST["remove"] stores the id of the to-be-removed work
            $results = $database->selectCustom("WORK", ["WORK.WORK_NAME", "THUMBNAIL.THUMB_LINK", "THUMBNAIL.THUMB_ID"], ["WORK_ID"], [$_POST["remove"]], ["="], "AND", ["THUMBNAIL"], ["WORK.THUMB_ID"], ["THUMBNAIL.THUMB_ID"]); 

            $_SESSION["location"] = "archives.php"; 
            $_SESSION["type"] = "work"; 

            $_SESSION["id"] = $_POST["remove"]; 
            $_SESSION["title"] = $results[0]["WORK_NAME"]; 
            $_SESSION["thumbLink"] = $results[0]["THUMB_LINK"]; 
            $_SESSION["thumbId"] = $results[0]["THUMB_ID"]; 

            $fileSystem->confirm(); 
        } else if (isset($_POST["yes"])) {
            // Executing a mysqldump
            // NOTE: Want this to iterate every day, not every delete
            // because if every delete, then can't restore multiple works
            // exec("C:/xampp/mysql/bin/mysqldump --user={$_SERVER['DB_USER']} --host={$_SERVER['DB_HOST']} {$_SERVER['DB_NAME']} --result-file=../backup/test.sql 2>&1", $output);

            $removed = $database->deleteValues("WORK", "WORK_ID", $_SESSION["id"]); 
            $used = $database->checkUsed($_SESSION["thumbId"]); 

            if (!$used) {
                $fileSystem->delete($_SESSION["thumbLink"]);
                $database->deleteValues("THUMBNAIL", "THUMB_ID", $_SESSION["thumbId"]); 
            }

            $database->cleanContributor(); 

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