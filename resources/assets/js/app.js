/**
 * Created by giulio on 12/03/2016.
 */

(function ($) {
    $('#app-navbar, #breadcrumbs').affix({
        offset: {
            top: 220
        }
    });

    if ($('#flash-notification .alert').length) {
        $('#flash-notification')
            .fadeTo(2000, 500)
            .slideUp(500, function () {
                $("#flash-notification").alert('close');
            });
    }
})(jQuery);