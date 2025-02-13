(function ($) {
    $(document).on('click', '[data-toggle="collapse-custom"]', function () {
        var val = $(this).attr('data-target');
        if ($(val).hasClass('displayed')) {
            $(val).removeClass('displayed');
        } else {
            $('.card').removeClass('displayed');
            $(val).addClass('displayed');
        }
    });

    $(document).on("change", "input[data-update]", function () {
        var destination = $(this).attr("data-update");
        $(destination).html($(this).val());
    });
})(jQuery);

