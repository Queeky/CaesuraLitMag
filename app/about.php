<?php 
    session_start(); 

    include("includes/connection.inc.php"); 

    if (isset($_POST["update"])) {
        $descript = $_POST["descript"]; 

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

        $updated = $database->updateValues("ABOUT", ["ABOUT_DESCRIPT"], [$descript]); 

        if ($updated) {
            echo "<p class='header-notif'>Successfully updated.</p>";
        } else {
            echo "<p class='header-notif'>Error with update.</p>"; 
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
        <title>ABOUT //</title>
    </head>
    <body>
        <?php 
            $title = "ABOUT"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <?php 
                    include("includes/about.inc.php"); 
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