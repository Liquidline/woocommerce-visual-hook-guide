jQuery(document).ready(function ($) {
    $('.open-hook').on('click', function () {
        var hook_name = $(this).data('hookname');
        var reffer = $(this).data('checkreffer');
        var ajaxurl = wvhg_ajax.ajaxurl;
        var data = {
            action: 'show_hooks',
            security: reffer,
            hook_name: hook_name
        };
        var result_add = $('#show-hooks-result');
        result_add.find('#list-hooks').remove();
        $('#wvhg-loader').show();
        $.post(ajaxurl, data, function (html, data, response) {
            $('#wvhg-loader').hide();
            result_add.html(html);



        });
    }).magnificPopup();
});