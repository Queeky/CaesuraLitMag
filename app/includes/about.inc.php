<?php 

function displayDescript($database) {
    $descripts = $database->selectCustom("ABOUT", ["ABOUT_DESCRIPT"]); 

    if ($descripts) {
        echo "<p class='about-page'>"; 

        foreach ($descripts as $item) {
            $description = htmlspecialchars_decode($item["ABOUT_DESCRIPT"]);
            echo $description; 
        }

        echo "</p>"; 
    } else {
        echo "<div class='empty-message large'>"; 
        echo "<p>Nothing's here at the moment!</p>"; 
        echo "</div>"; 
    }
}

function displayForm($database) {
    $descripts = $database->selectCustom("ABOUT", ["*"]);

    echo "<form action='about.php' method='POST'>"; 
    echo "<textarea class='edit' name='descript'>"; 

    foreach ($descripts as $item) {
        $descript = htmlspecialchars_decode($item["ABOUT_DESCRIPT"]);
        $descript = str_replace("<br />", "", $descript); 

        echo $descript; 
    }

    echo "</textarea>"; 

    echo "<button class='submit-btn' type='submit' name='update' value=$item[ABOUT_ID]>Update</button>"; 
    echo "</form>"; 

}

// Displaying the edit form if logged in
if (isset($_SESSION["admName"])) {
    displayForm($database); 
} else {
    displayDescript($database); 
}
?>