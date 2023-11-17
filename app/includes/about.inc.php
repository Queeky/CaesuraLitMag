<?php 

function displayDescript($database) {
    $descripts = $database->selectCustom("ABOUT", ["ABOUT_DESCRIPT"]); 

    if ($descripts) {
        echo "<p class='about-page'>"; 

        foreach ($descripts as $item) {
            echo "$item[ABOUT_DESCRIPT]"; 
        }

        echo "</p>"; 
    } else {
        echo "<p class='empty-message'>"; 
        echo "Nothing's here right now!"; 
        echo "</p>"; 
    }
}

function displayForm($database) {
    $descripts = $database->selectCustom("ABOUT", ["ABOUT_DESCRIPT"]);

    echo "<form action='about.php' method='POST'>"; 
    echo "<textarea class='edit' name='descript'>"; 

    // Set a var to hold all info (maybe)
    // ALSO needs to be able to convert empty lines to <br><br>
    // ALSO delete and upload pictures

    foreach ($descripts as $item) {
        echo "$item[ABOUT_DESCRIPT]"; 
    }

    echo "</textarea>"; 

    echo "<button class='submit-btn' type='submit' name='update'>Update</button>"; 
    echo "</form>"; 

}

// Displaying the edit form if logged in
if (isset($_SESSION["admName"])) {
    displayForm($database); 
} else {
    displayDescript($database); 
}
?>