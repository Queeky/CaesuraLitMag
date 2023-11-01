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
        <title>ISSUES //</title>
    </head>
    <body>
        <?php 
            $title = "ISSUES"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left issue-page'>
                <?php 
                    $results = $database->selectAllIssues(); 
                    include("includes/issue.inc.php"); 

                    displayIssue($results); 
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