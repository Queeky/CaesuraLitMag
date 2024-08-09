$(function(){
    $(".mobile-menu-link").click(function(){
        $(".mobile-dropdown").toggle();
    });
 });

 $(function(){
    $(".upload-type-lit").click(function(){
        $(".upload-input #lit").css("display", "block"); 
        $(".upload-input #art").css("display", "none");
    });

    $(".upload-type-art").click(function(){
        $(".upload-input #lit").css("display", "none"); 
        $(".upload-input #art").css("display", "block"); 
    });
 }); 