function filter() {
    $(".filter-by-type>li>a").click(function(){
        $("#favorite-container").fadeOut(300);
        $("#preloader").fadeIn(1000);
        var link = $(this).attr("data-link");
        console.log(link);
        $.ajax({url: link, success: function(result){
            $("#favorite-container").html(result);
            $("#preloader").fadeOut(300);
            $("#favorite-container").fadeIn(1000);
        }});
    });
}

function addToFavorite() {
    $(".add-to-favorite").click(function(){
        var link = $(this).attr("data-link");
        console.log(link);
        $.ajax({url: link, success: function(){
            console.log("OK");
        }});
    });
}

filter();
addToFavorite();