<?php 
    session_start(); 

    include("includes/connection.inc.php"); 

    if (isset($_POST["update"])) {
        $id = $_POST["update"]; 
        $descript = $_POST["descript"]; 

        if ($descript) {
            $updated = $database->updateValues("GUIDELINE", ["GUIDE_DESCRIPT"], [$descript], ["GUIDE_ID"], [$id]); 

            if ($updated) {
                echo "<p class='header-notif'>Successfully updated.</p>";
            } else {
                echo "<p class='header-notif'>Error with update.</p>"; 
            }
        } else {
            echo "<p class='header-notif'>A field is missing information.</p>"; 
        }
    }

    if (isset($_POST["remove"])) {
        $id = $_POST["remove"]; 
        
        $removed = $database->deleteValues("GUIDELINE", "GUIDE_ID", $id); 

        if ($removed) {
            echo "<p class='header-notif'>Guideline successfully removed.</p>";
        } else {
            echo "<p class='header-notif'>Error removing guideline from database.</p>"; 
        }
    }

    if (isset($_POST["add"])) {
        $descript = $_POST["descript"]; 

        if ($descript) {
            $added = $database->insertValues("GUIDELINE", ["GUIDE_DESCRIPT"], [$descript]); 

            if ($added) {
                echo "<p class='header-notif'>Guideline successfully added.</p>";
            } else {
                echo "<p class='header-notif'>Error pushing guideline to database.</p>"; 
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
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <title>SUBMISSIONS //</title>
    </head>
    <body>
        <?php 
            $title = "SUBMISSIONS"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <?php
                    include("includes/guidelines.inc.php"); 
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