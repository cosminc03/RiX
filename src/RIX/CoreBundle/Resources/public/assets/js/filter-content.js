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

function loadMore() {
    $(".more-items").on("click", ".view-more", function(){
        var link = $(this).attr("data-link");
        $(this).remove();

        $("#preloader").fadeIn(1000);
        $.ajax({url: link, success: function(result){
            $("#preloader").remove();
            $(".more-items").append(result);
            $("#preloader").fadeOut(300);
            $(".more-items").fadeIn(1000);
        }});
    });
}

filter();
addToFavorite();
loadMore();