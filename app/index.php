<?php 
    session_start(); 
    include("includes/connection.inc.php"); 
    include("includes/files.inc.php"); 
    include("includes/mail.inc.php"); 

    function readAction($database, $mail) {
        if (isset($_POST['email-submit'])) { 
            $email = $_POST['email-signup']; 

            if ($email) {
                $check = null; 
                $results = $database->selectCustom("EMAIL", ["*"]); 

                // Checking if email already exists in database
                foreach ($results as $item) {
                    if ($item["EMAIL_ADDRESS"] == $email) {
                        $check = true; 
                    }
                }
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<p class='header-notif'>That is not a valid email address format.</p>";
                } else if ($check) {
                    echo "<p class='header-notif'>$email is already registered.</p>";
                } else {
                    $added = $database->insertValues("EMAIL", ["EMAIL_ADDRESS"], [$email]); 

                    if ($added) {
                        echo "<p class='header-notif'>$email successfully registered.</p>";
                    } else {
                        echo "<p class='header-notif'>Error registering $email.</p>";
                    }
                }
            } else {
                echo "<p class='header-notif'>A field is missing information.</p>";
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
        <title>CAESURA //</title>
    </head>
    <body>
        <?php 
            readAction($database, $mail); 

            $title = "CAESURA"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap float-left'>

                <?php 
                    $results = $database->selectRecentIssue(); 
                    include("includes/issue.inc.php"); 

                    displayIssue($results); 
                    displayWorks($results, $database); 
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