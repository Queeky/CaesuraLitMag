<?php 
    session_start(); 

    include("includes/connection.inc.php"); 

    if (isset($_POST["update"])) {
        $id = $_POST["update"]; 
        $title = $_POST["title"]; 
        $fname = $_POST["fname"]; 
        $lname = $_POST["lname"]; 
        $phone = $_POST["phone"]; 
        $email = $_POST["email"]; 
        $updated = null; 

        if ($title && $phone && $email) {
            if ($fname && $lname) {
                $added = $database->updateValues("CONTACT", ["CONTACT_TITLE", "CONTACT_FNAME", "CONTACT_LNAME", "CONTACT_PHONE", "CONTACT_EMAIL"], [$title, $fname, $lname, $phone, $email], ["CONTACT_ID"], [$id]); 
            } else if (($fname && !$lname) || (!$fname && $lname)) {
                echo "<p class='header-notif'>If adding name, you must include the first and last name.</p>";
            } else {
                $added = $database->updateValues("CONTACT", ["CONTACT_TITLE", "CONTACT_PHONE", "CONTACT_EMAIL"], [$title, $phone, $email], ["CONTACT_ID"], [$id]); 
            }

            $updated = $database->updateValues("CONTACT", ["CONTACT_TITLE", "CONTACT_FNAME", "CONTACT_LNAME", "CONTACT_PHONE", "CONTACT_EMAIL"], [$title, $fname, $lname, $phone, $email], ["CONTACT_ID"], [$id]); 

            if ($updated) {
                echo "<p class='header-notif'>Successfully updated.</p>";
            } else {
                echo "<p class='header-notif'>Error with update.</p>"; 
            }
        } else {
            echo "<p class='header-notif'>A field is missing information.</p>"; 
        }
    }

    if (isset($_POST["remove"])) {
        $id = $_POST["remove"]; 
        $title = $_POST["title"]; 

        // Remember to remove img too
        $removed = $database->deleteValues("CONTACT", "CONTACT_ID", $id); 

        if ($removed) {
            echo "<p class='header-notif'>$title successfully removed.</p>";
        } else {
            echo "<p class='header-notif'>Error removing $title from database.</p>"; 
        }
    }

    if (isset($_POST["add"])) {
        $title = $_POST["title"]; 
        $fname = $_POST["fname"]; 
        $lname = $_POST["lname"]; 
        $phone = $_POST["phone"]; 
        $email = $_POST["email"]; 

        $added = null; 

        if ($title && $email) {
        

            if ($fname && $lname && $phone) {
                $added = $database->insertValues("CONTACT", ["CONTACT_TITLE", "CONTACT_FNAME", "CONTACT_LNAME", "CONTACT_PHONE", "CONTACT_EMAIL"], [$title, $fname, $lname, $phone, $email]); 
            } else if ($fname && $lname) {
                $added = $database->insertValues("CONTACT", ["CONTACT_TITLE", "CONTACT_FNAME", "CONTACT_LNAME", "CONTACT_EMAIL"], [$title, $fname, $lname, $email]); 
            } else if (($fname && !$lname) || (!$fname && $lname)) {
                echo "<p class='header-notif'>If adding name, you must include the first and last name.</p>";
            } else if ($phone) {
                $added = $database->insertValues("CONTACT", ["CONTACT_TITLE", "CONTACT_PHONE", "CONTACT_EMAIL"], [$title, $phone, $email]); 
            } else {
                $added = $database->insertValues("CONTACT", ["CONTACT_TITLE", "CONTACT_EMAIL"], [$title, $email]); 
            }

            if ($added) {
                echo "<p class='header-notif'>$title successfully added.</p>";
            } else {
                echo "<p class='header-notif'>Error pushing $title to database.</p>"; 
            }
        } else {
            echo "<p class='header-notif'>A field is missing information.</p>"; 
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>CONTACTS //</title>
    </head>
    <body>
        <?php 
            $title = "CONTACTS"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <?php 
                    include("includes/contacts.inc.php"); 
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