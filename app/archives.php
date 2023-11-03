<?php 
    session_start(); 
    include("includes/connection.inc.php"); 

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