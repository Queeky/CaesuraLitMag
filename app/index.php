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
        <title>CAESURA //</title>
    </head>
    <body>
        <?php 
            $title = "CAESURA"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap float-left'>

                <?php 
                    $results = $database->selectRecentIssue(); 
                    include("includes/issue.inc.php"); 
                ?>
                <div class='issue-works'>
                    <ul>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                        <li>
                            <p class='title'>TWILIGHT STADIUM</p>
                            <p class='contributor'>Quinn Miersma</p>
                        </li>
                    </ul>
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