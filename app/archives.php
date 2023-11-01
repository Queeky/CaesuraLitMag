<?php 
    session_start(); 
    include("includes/connection.inc.php"); 

    $query = null; 

    if (isset($_GET['query'])) {
        $query = $_GET['query']; 
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