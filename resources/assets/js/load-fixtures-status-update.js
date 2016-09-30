/**
 * Created by Giulio Troccoli-Allard <giulio@troccoli.it> on 10/09/2016.
 */
(function ($) {
    "use strict";

    var apiToken = $('#api_token').val(),
        jobId = $('#job_id').val();

    var poll = function poll() {

        $.get({
            'url'     : '/api/v1/upload_status.json',
            'data'    : {
                'job'      : jobId,
                'api_token': apiToken
            },
            'dataType': 'json'
        }).done(function (data) {
            if (data.error) {
                $('#message').html(data.message);
            } else {
                $('#message').html(data.status.message);
                $('.progress-bar').css('width', data.status.progress + '%').attr('aria-valuenow', data.status.progress)
                    .find('.sr-only').html(data.status.progress + '% Complete');
                setTimeout(poll, 1000);
            }
        });
    };

    poll();

})(jQuery);