<?php
    session_start(); 

    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <title>CONFIRM //</title>
    </head>
    <body>
        <?php
            $title = "CONFIRM"; 
            include("includes/nav.inc.php");
        ?>
        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <div class='delete-warning'>
                        <?php 
                            $warningMsg = ""; 

                            switch ($_SESSION["type"]) {
                                case "work": 
                                    $warningMsg = "$_SESSION[title] will be deleted. Do you want to continue?"; 
                                    break; 
                                case "issue": 
                                    $warningMsg = "Deleting $_SESSION[title] will also remove all works within this issue. Do you want to continue?"; 
                                    break; 
                                case "media": 
                                    $warningMsg = "Deleting $_SESSION[title] will also remove all works of this media type. Do you want to continue?"; 
                                    break; 
                            }

                            echo "<form action='$_SESSION[location]' method='POST'>"; 

                            echo "<div>"; 
                            echo "<p>$warningMsg</p>"; 
                            echo "<div>"; 
                            echo "<button class='submit-btn' type='submit' name='yes'>Yes</button>"; 
                            echo "<button class='submit-btn' type='submit' name='no'>No</button>"; 
                            echo "</div>"; 
                            echo "</div>"; 

                            echo "</form>"; 
                        ?>
                </div>
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