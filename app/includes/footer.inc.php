<?php
    function displayFooter() {
        echo "<footer>"; 

        echo "<div class='footer-socials'>"; 
        echo "<a href='https://iwumllsylva.wixsite.com/sylva'><img id='blog' src='images/CaesuraIcon2.png' alt='MLL blog icon'></a>";
        echo "<a href='https://www.facebook.com/caesura.iwu/'><img id='facebook' src='images/FacebookIcon.png' alt='Facebook icon'></a>"; 
        echo "<a href='https://www.instagram.com/caesuramagazine/?hl=en'><img id='instagram' src='images/InstagramIcon.png' alt='Instagram icon'></a>";
        echo "</div>"; 

        echo "<div class='footer-contact'>"; 
        echo "<a href='tel:7656772712'>(765) 677 2712</a>"; 
        echo "<a href='mailto:caesura@indwes.edu'>caesura@indwes.edu</a>"; 
        echo "<button><a href='login.php' target='_blank'>Admin</a></button>"; 
        echo "</div>"; 

        echo "</footer>"; 
    }

    displayFooter(); 
?>