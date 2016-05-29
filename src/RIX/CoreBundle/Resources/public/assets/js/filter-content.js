function filter() {
    $(".filter-by-type>li>a").click(function(){
        var link = $(this).attr("data-link");
        console.log(link);
        $.ajax({url: link, success: function(result){
            $("#div-test").html(result);
        }});
    });
}
filter();