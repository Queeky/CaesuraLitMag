<?php

$guidelines = $database->selectCustom("GUIDELINE", ["*"]); 

// Displaying guidelines as seen by user
function displayGuidelines($guidelines) {
    echo "<div class='guidelines'>"; 
    echo "<p><strong>"; 
    echo "Please read carefully and follow the submission instructions. We cannot consider your submission until you have followed the submission instructions."; 
    echo "</strong></p>"; 

    echo "<ul class='guidelines'>"; 

    if ($guidelines) {
        foreach ($guidelines as $guide) {
            echo "<li>$guide[GUIDE_DESCRIPT]</li>"; 
        }
    } else {
        echo "<div class='empty-message large'>"; 
        echo "<p>Nothing's here at the moment!</p>"; 
        echo "</div>";
    }

    echo "</ul>"; 
    echo "<button class='submit-btn submittable'><a href='https://caesuraliterarymagazine.submittable.com/submit' target='_blank'>Submit your work</a></button>"; 
    echo "</div>";
}

// Displaying guidelines as seen by admin
function displayForm($guidelines) {
    echo "<div class='add-form guidelines'>"; 
    echo "<form action='submissions.php' method='POST'>"; 
    echo "<h3>ADD NEW GUIDELINE:</h3>"; 

    echo "<textarea name='descript' placeholder='Enter new guideline here ***'></textarea>"; 
    echo "<button class='submit-btn' type='submit' name='add'>Submit</button>"; 

    echo "</form>"; 
    echo "</div>"; 

    echo "<div class='guidelines'>"; 

    if ($guidelines) {
        foreach ($guidelines as $guide) {
            echo "<div class='guide-info'>"; 
            echo "<form action='submissions.php' method='POST'>"; 
            echo "<textarea name='descript'>$guide[GUIDE_DESCRIPT]</textarea>"; 
    
            echo "<button class='submit-btn' type='submit' value='$guide[GUIDE_ID]' name='update'>Update</button>"; 
            echo "<button class='submit-btn' type='submit' value='$guide[GUIDE_ID]' name='remove'>Remove</button>"; 
            echo "</form>"; 
            echo "</div>"; 
        }
    } else {
        echo "<div class='empty-message large'>"; 
        echo "<p>Nothing's here at the moment!</p>"; 
        echo "</div>";
    }

    echo "</div>"; 
}

if (isset($_SESSION["admName"])) {
    displayForm($guidelines); 
} else {
    displayGuidelines($guidelines); 
}

?>