<?php 
    session_start(); 
    include("includes/connection.inc.php"); 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <title>HIGHLIGHT //</title>
    </head>
    <body>
        <?php 
            $title = "HIGHLIGHT"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <?php 
                    include("includes/work.inc.php"); 
                ?>
            </div>
        </div>
        <?php
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>