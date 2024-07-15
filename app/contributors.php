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
        <title>CONTRIBUTORS //</title>
    </head>
    <body>
        <?php 
            $title = "CONTRIBUTORS"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <?php 
                    $contributors = $database->selectCustom("CONTRIBUTOR", ["*"],  order: "CON_LNAME"); 

                    if ($contributors) {
                        foreach ($contributors as $con) {
                            echo "<div class='contributor-item'>"; 
                            echo "<p>$con[CON_LNAME], $con[CON_FNAME] </p>"; 
                            echo "</div>"; 
                        }
                    } else {
                        echo "<div class='empty-message large'>"; 
                        echo "<p>Nothing's here at the moment!</p>"; 
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