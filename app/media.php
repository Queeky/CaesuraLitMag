<?php 
    session_start(); 

    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 

    function readAction($database, $fileSystem) {
        if (isset($_POST["add"])) {
            $name = $_POST["name"]; 
    
            if ($name) {
                // Formatting data
                $name = ucfirst(strtolower($name));
    
                $added = $database->insertValues("MEDIA_TYPE", ["MEDIA_NAME"], [$name]); 

                if ($added) {
                    echo "<p class='header-notif'>$name successfully added.</p>";
                } else {
                    echo "<p class='header-notif'>Error pushing $name to database.</p>"; 
                }
            } else {
                echo "<p class='header-notif'>A field is missing information.</p>";
            }
        } else if (isset($_POST["remove"])) {
            $id = $_POST["remove"]; 
            $name = null; 

            $media = $database->selectCustom("MEDIA_TYPE", ["*"], ["MEDIA_ID"], [$id], ["="]); 

            foreach ($media as $medium) {
                $name = $medium["MEDIA_NAME"]; 
            }

            $_SESSION["location"] = "media.php"; 
            $_SESSION["type"] = "media"; 

            $_SESSION["id"] = $id; 
            $_SESSION["title"] = $name; 

            $fileSystem->confirm(); 
        } else if (isset($_POST["yes"])) {
            $removed = $database->deleteValues("MEDIA_TYPE", "MEDIA_ID", $_SESSION["id"]); 
    
            if ($removed) {
                echo "<p class='header-notif'>$_SESSION[title] successfully removed.</p>";
            } else {
                echo "<p class='header-notif'>Error removing $_SESSION[title] from database.</p>"; 
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
        <title>MEDIA //</title>
    </head>
    <body>
        <?php 
            readAction($database, $fileSystem); 

            $title = "MEDIA"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <?php 
                    echo "<div class='add-form media'>"; 
                    echo "<form action='media.php' method='POST' enctype='multipart/form-data'>"; 
                    echo "<h3>ADD NEW MEDIA TYPE:</h3>"; 
            
                    echo "<input type='text' name='name' placeholder='Enter media name ***'>"; 
                    echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 
            
                    echo "</form>"; 
                    echo "</div>"; 

                    $media = $database->selectCustom("MEDIA_TYPE", ["*"]); 

                    foreach ($media as $medium) {
                        echo "<div class='media-item'>"; 
                        echo "<form action='media.php' method='POST'>"; 
                        echo "<p>$medium[MEDIA_NAME]</p>"; 
                        echo "<button type='submit' class='submit-btn' name='remove' value='$medium[MEDIA_ID]'>Remove</button>"; 
                        echo "</form>"; 
                        echo "</div>"; 
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