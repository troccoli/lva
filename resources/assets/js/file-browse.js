/**
 * Created by Giulio Troccoli <giulio@troccoli.it> on 25/08/2016.
 *
 * https://www.abeautifulsite.net/whipping-file-inputs-into-shape-with-bootstrap-3
 */
(function ($) {
    // We can attach the `fileselect` event to all file inputs on the page
    $('form').on('change', ':file', function () {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });

    // We can watch for our custom `fileselect` event like this
    $(document).ready(function () {
        $(':file').on('fileselect', function (event, numFiles, label) {

            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

            if (input.length) {
                input.val(log);
            } else {
                if (log) alert(log);
            }

        });
    });
})(jQuery);