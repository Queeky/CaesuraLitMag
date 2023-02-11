<?php 
    include("includes/connection.inc.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Caesura</title>
        <link rel="stylesheet" href="css/styleMegalodon.css?v=1">
    </head>
    <body>

        <?php 
        include("includes/header.inc.php");
        ?>
        
        <div class="pageName">
            <div class="pageMessage">
                <h1>Caesura //</h1>
            </div>
        </div>
        <div id="issueDisplayed">
            <h3>2023 Issue -- (name)</h3>
            <a href="#"><img src="#"></a>
            <span><p>Description with read more link prompt here</p></span>
        </div>
        <div id="currentNotifs">
            <h3>2023 Notifications</h3>
            <?php
                // Loop through all notifs contained in db
            ?> 
        </div>
        <footer>
            <a href="#" id="facebook"><img src="#"></a>
            <a href="#" id="instagram"><img src="#"></a>
            <a href="#" id="sylva"><img src="#"></a>
            <p id="contactNumber">### - ####</p>
            <p id="contactEmail">fake.email@gmail.com</p>
        </footer>

    </body>
</html>