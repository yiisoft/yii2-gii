

$(document).ready(function () {
    jQuery(document).on('click', '[data-toggle="collapse-custom"]', function () {
        var val = $(this).attr('data-target');
        if($(val).hasClass('displayed')){
            $(val).removeClass('displayed');
            // $(this).addClass('collapsed');
        }else {
            $('.card').removeClass('displayed');
            $(val).addClass('displayed');
            // $(this).removeClass('collapsed');
        }
    });
});

$(document).ready(function(){
    $(document).on("change", ".js-property-name", function(){
        var index = $(this).attr("data-index");
        var value = $(this).val();
        $("#property_holder_" + index).html(value);
    });
});
