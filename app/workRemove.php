<?php
    session_start(); 
    include("includes/connection.inc.php"); 

    if (isset($_POST["workRemove"])) {
        $workId = $_POST["workRemove"];
        $workName = null; 
        $names = $database->selectCustom("WORK", ["WORK_NAME"], ["WORK_ID"], [$workId], ["="]); 

        foreach ($names as $name) {
            $workName = $name["WORK_NAME"]; 
        }

        // Put some kind of safety precautions
        $removed = $database->deleteValues("WORK", "WORK_ID", $workId); 

        if ($removed) {
            echo "<p class='header-notif'>$workName successfully removed.</p>";
        } else {
            echo "<p class='header-notif'>Error removing $workName from database.</p>"; 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>REMOVE WORKS //</title>
    </head>
    <body>
        <?php 
            $title = "REMOVE WORKS"; 
            include("includes/nav.inc.php"); 
        ?>
        <div class='page-wrap'>
            <div class='content-wrap'>
                <div class='form'>
                    <form action="workRemove.php" method='POST'>
                        <label for="">WORKS: </label>
                        <ul>
                            <?php
                                $works = $database->selectCustom("WORK", ["WORK_ID", "WORK_NAME"]); 

                                foreach($works as $work) {
                                    echo "<li>$work[WORK_NAME]</li><button type='submit' name='workRemove' value='$work[WORK_ID]'>Remove</button>"; 
                                }
                            ?>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
        <?php 
            include("includes/footer.inc.php"); 
        ?>
    </body>
</html>