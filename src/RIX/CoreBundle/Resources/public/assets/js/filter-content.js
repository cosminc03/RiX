function filter() {
    $(".filter-by-type>li>a").click(function(){
        $("#favorite-container").fadeOut(300);
        $("#preloader").fadeIn(1000);
        var link = $(this).attr("data-link");
        $.ajax({url: link, success: function(result){
            $("#favorite-container").html(result);
            $("#preloader").fadeOut(300);
            $("#favorite-container").fadeIn(1000);
        }});
    });
}

function addToFavorite() {
    $(document).on({
        click: function(){
            var $this = $(this);
            $(this).css({
                "width":"43px",
                "height":"43px"
            });
            setTimeout(function() {
                $this.css({
                    "width":"35px",
                    "height":"32px"
                });
            }, 600);
            var link = $(this).attr("data-link");
            $.ajax({url: link, success: function(){
            }});
        }
    },".add-to-favorite");
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

function removeFavorite()
{
    $(document).on({
        click: function() {
            var dataRemove = $(this).attr('data-link');
            var dataReload = $(this).attr('data-reload');

            $(document).find("#favorite-container").fadeOut(300);
            $(document).find("#preloader").fadeIn(1000);

            $.ajax({url: dataRemove, success: function(){
                $.ajax({url: dataReload, success: function(result){
                    $(document).find("#favorite-container").html(result);
                    $(document).find("#preloader").fadeOut(300);
                    $(document).find("#favorite-container").fadeIn(1000);
                }});
            }});
        }
    }, '.rix-remove-favorite>img');
}

filter();
addToFavorite();
loadMore();
removeFavorite();