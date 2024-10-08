<?php
    session_start(); 

    include("includes/connection.inc.php"); 

    if (isset($_POST["loginSubmit"])) {
        $name = $_POST["admName"];
        $pass = $_POST["admPass"]; 
        $realPass = null; 

        $pass = md5($pass); 

        if (!empty($name) && !empty($pass)) {
            $result = $database->selectCustom("ADMIN", ["ADM_PASS"], ["ADM_NAME"], [$name], ["="]); 

            foreach ($result as $item) {
                $realPass = $item["ADM_PASS"]; 
            }

            if ($pass == $realPass) {
                $_SESSION["admName"] = $name; 
                echo "<meta http-equiv='refresh' content='0; URL=index.php'>"; 
            } else {
                echo "<p class='header-notif'>Password or username is incorrect.</p>"; 
            }
        } else {
            echo "<p class='header-notif'>A field is missing information.</p>"; 
        }
    }

    if (isset($_POST["logoutSubmit"])) {
        session_unset(); 
        echo "<meta http-equiv='refresh' content='0; URL=index.php'>";
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <title>LOGIN //</title>
    </head>
    <body>
        <?php 
            $title = "LOGIN"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='add-form login'>
                    <?php 
                        echo "<form action='login.php' method='POST'>";

                        if (!isset($_SESSION["admName"])) {
                            echo "<label for='admName'>USERNAME: </label>";
                            echo "<input type='text' name='admName' placeholder='Enter username'>"; 
                            echo "<label for='admPass'>PASSWORD: </label>"; 
                            echo "<input type='password' name='admPass' placeholder='Enter password'>";
                            echo "<button class='submit-btn' type='submit' name='loginSubmit'>Log In</button>";  
                        } else {
                            echo "<button class='submit-btn' type='submit' name='logoutSubmit'>Log Out</button>"; 
                        }

                        echo "</form>"; 
                    ?>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>