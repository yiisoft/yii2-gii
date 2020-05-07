(function ($) {
    $(document).on('click', '[data-toggle="collapse-custom"]', function () {
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

    $(document).on("change", "input[data-update]", function(){
        var destination = $(this).attr("data-update");
        var value = $(this).val();
        $(destination).html(value);
    });
})(jQuery);

