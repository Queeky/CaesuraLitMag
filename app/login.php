<?php
    session_start(); 

    include("includes/connection.inc.php"); 

    if (isset($_POST["login-submit"])) {
        $name = $_POST["admName"];
        $pass = $_POST["admPass"]; 

        if (!empty($name) && !empty($pass)) {
            $result = $database->selectCustom("ADMIN", ["ADM_ID"], ["ADM_NAME", "ADM_PASS"], ["$_POST[admName]", "$_POST[admPass]"], ["=", "="], "AND"); 

            if ($result != null) {
                $_SESSION["admName"] = $name; 
                echo "<meta http-equiv='refresh' content='0; URL=index.php'>"; 
            } else {
                echo "<p>Password or username is incorrect.</p>"; 
            }
        } else {
            echo "<p>Both fields need input.</p>"; 
        }
    }

    if (isset($_POST["logout-submit"])) {
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
        <title>LOGIN //</title>
    </head>
    <body>
        <?php 
            $title = "LOGIN"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='login-form'>
                    <?php 
                        echo "<form action='login.php' method='POST'>";

                        if (!isset($_SESSION["admName"])) {
                            echo "<label for='admName'>USERNAME: </label>";
                            echo "<input type='text' name='admName' placeholder='Enter username'>"; 
                            echo "<label for='admPass'>PASSWORD: </label>"; 
                            echo "<input type='password' name='admPass' placeholder='Enter password'>";
                            echo "<button type='submit' name='login-submit'>LOG IN</button>";  
                        } else {
                            echo "<button type='submit' name='logout-submit'>LOG OUT</button>"; 
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