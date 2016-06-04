/*function filter() {
    $(".filter-by-type>li>a").click(function(){
        $("#div-test").fadeOut(300);
        $("#preloader").fadeIn(1000);
        var link = $(this).attr("data-link");
        console.log(link);
        $.ajax({url: link, success: function(result){
            $("#div-test").html(result);
            $("#preloader").fadeOut(300);
            $("#div-test").fadeIn(1000);
        }});
    });
}
filter();*/
function addToFavorite() {
    $(".add-to-favorite").click(function(){
        var link = $(this).attr("data-link");
        console.log(link);
        $.ajax({url: link, success: function(){
            console.log("OK");
        }});
    });
}
addToFavorite();