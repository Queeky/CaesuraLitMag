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
        <title>CONTACTS //</title>
    </head>
    <body>
        <?php 
            $title = "CONTACTS"; 
            include("includes/nav.inc.php"); 
        ?>

        <div class='page-wrap'>
            <div class='content-wrap float-left'>
                <div class='contact-item'>
                    <img src="#" alt="">
                    <div class='contact-info'>
                        <p>Head of Evil Incorporated</p>
                        <p>Heinz Doofenshmirtz</p>
                        <a href="tel:2222222222">(222) 222 2222</a>
                        <a href="mailto:goofydoofy@outlook.com">goofydoofy@outlook.com</a>
                    </div>
                </div>
                <div class='contact-item'>
                    <img src="#" alt="">
                    <div class='contact-info'>
                        <p>Head of Evil Incorporated</p>
                        <p>Heinz Doofenshmirtz</p>
                        <a href="tel:2222222222">(222) 222 2222</a>
                        <a href="mailto:goofydoofy@outlook.com">goofydoofy@outlook.com</a>
                    </div>
                </div>
                <div class='contact-item'>
                    <img src="#" alt="">
                    <div class='contact-info'>
                        <p>Head of Evil Incorporated</p>
                        <p>Heinz Doofenshmirtz</p>
                        <a href="tel:2222222222">(222) 222 2222</a>
                        <a href="mailto:goofydoofy@outlook.com">goofydoofy@outlook.com</a>
                    </div>
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