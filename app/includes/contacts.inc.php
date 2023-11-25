<?php 

$contacts = $database->selectCustom("CONTACT", ["*"], jTable: ["THUMBNAIL"], jColumn1: ["CONTACT.THUMB_ID"], jColumn2: ["THUMBNAIL.THUMB_ID"]); 

// Displaying contacts as seen by user
function displayContacts($contacts) {
    if ($contacts) {
        foreach ($contacts as $con) {
            echo "<div class='contact-item'>"; 
        
            // echo "<img style='width: 8vm;' src='$con[THUMB_LINK]' alt='$con[THUMB_DESCRIPT]'>"; 
            echo "<div class='contact-info'>"; 
            echo "<p>$con[CONTACT_TITLE]</p>"; 
            echo "<p>$con[CONTACT_FNAME] $con[CONTACT_LNAME]</p>"; 
            echo "<a href='tel:$con[CONTACT_PHONE]'>$con[CONTACT_PHONE]</a>"; 
            echo "<a href='mailto:$con[CONTACT_EMAIL]'>$con[CONTACT_EMAIL]</a>";
            echo "</div>"; 
    
            echo "</div>"; 
        }
    } else {
        echo "<div class='empty-message large'>"; 
        echo "<p>Nothing's here at the moment!</p>"; 
        echo "</div>";
    }
}

// Displaying contacts as seen by admin
function displayForm($contacts) {
    echo "<div class='add-form contacts'>"; 
    echo "<form action='contacts.php' method='POST'>"; 
    echo "<h3>ADD NEW CONTACT:</h3>"; 

    echo "<input type='text' name='title' placeholder='Title'>"; 
    echo "<input type='text' name='fname' placeholder='First Name'>"; 
    echo "<input type='text' name='lname' placeholder='Last Name'>"; 
    echo "<input type='text' name='phone' placeholder='Phone'>"; 
    echo "<input type='text' name='email' placeholder='Email'>"; 
    echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

    echo "</form>"; 
    echo "</div>"; 


    if ($contacts) {
        foreach ($contacts as $con) {
            echo "<div class='contact-item'>"; 
    
            // echo "<img style='width: 8vm;' src='$con[THUMB_LINK]' alt='$con[THUMB_DESCRIPT]'>";
            echo "<div class='contact-info'>"; 
            echo "<form action='contacts.php' method='POST'>"; 
    
            echo "<input type='text' name='title' value='$con[CONTACT_TITLE]' placeholder='Title'>"; 
            echo "<input type='text' name='fname' value='$con[CONTACT_FNAME]' placeholder='First Name'>"; 
            echo "<input type='text' name='lname' value='$con[CONTACT_LNAME]' placeholder='Last Name'>"; 
            echo "<input type='text' name='phone' value='$con[CONTACT_PHONE]' placeholder='Phone'>"; 
            echo "<input type='text' name='email' value='$con[CONTACT_EMAIL]' placeholder='Email'>"; 
    
            echo "<div class='controls'>"; 
            echo "<button class='submit-btn' type='submit' value='$con[CONTACT_ID]' name='update'>Update</button>"; 
            echo "<button class='submit-btn' type='submit' value='$con[CONTACT_ID]' name='remove'>Remove</button>";
            echo "</div>"; 
    
            echo "</form>"; 
            echo "</div>"; 
    
            echo "</div>";
        } 
    } else {
        echo "<p class='empty-message'>Nothing's here at the moment!</p>"; 
    }
}

if (isset($_SESSION["admName"])) {
    displayForm($contacts); 
} else {
    displayContacts($contacts); 
}
?>