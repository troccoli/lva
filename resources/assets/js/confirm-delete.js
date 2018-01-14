/**
 * Created by giulio on 03/04/2016.
 */


(function ($) {
    $('[data-toggle=confirmation]').confirmation({
        rootSelector: '[data-toggle=confirmation]',
        singleton: true,
        popout   : true
    });
})(jQuery);