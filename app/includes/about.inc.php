<?php 

function displayDescript($database) {
    $descripts = $database->selectCustom("ABOUT", ["ABOUT_DESCRIPT"]); 

    if ($descripts) {
        echo "<p class='about-page'>"; 

        echo "<img id='mll-lobby' src='images/MLLLobby.jpg' alt='Image of the MLL lobby entrance.'>"; 

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
    $descripts = $database->selectCustom("ABOUT", ["ABOUT_DESCRIPT"]);

    echo "<form action='about.php' method='POST'>"; 
    echo "<textarea class='edit' name='descript'>"; 

    foreach ($descripts as $item) {
        // $description = htmlspecialchars_decode($item["ABOUT_DESCRIPT"]); 
        // $description = str_replace("&lt;br /&gt;", "\n", $item["ABOUT_DESCRIPT"]); 
        echo $item["ABOUT_DESCRIPT"]; 
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