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
    
                            echo "<div class='contributor-info'>"; 
                            echo "<p>$con[CON_LNAME], $con[CON_FNAME] </p>"; 
                            
                            // Checking if phone num is available
                            // Maybe changing this so db holds "unavailable" if no 
                            // phone or email (default value)
                            if ($con["CON_PHONE"]) {
                                echo "<a href='tel:$con[CON_PHONE]'>$con[CON_PHONE]</a>"; 
                            } else {
                                echo "<p>0000000000</p>"; 
                            }
    
                            // Checking if email is available
                            if ($con["CON_EMAIL"]) {
                                echo "<a href='mailto:$con[CON_EMAIL]'>$con[CON_EMAIL]</a>";
                            } else {
                                echo "<p>No available email</p>"; 
                            }
    
                            echo "</div>"; 
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