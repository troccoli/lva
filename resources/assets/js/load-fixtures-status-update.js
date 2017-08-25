/**
 * Created by Giulio Troccoli-Allard <giulio@troccoli.it> on 10/09/2016.
 */
(function ($) {
    "use strict";

    var apiToken = $('#api_token').val(),
        jobId = $('#job_id').val(),
        userActionModal = $('#user-action-modal'),
        userConfirmationModal = $('#user-confirmation-modal'),
        lastTimestamp = 0,

        // These variables reflects the statuses maintaned in the UploadJobStatus model class
        statusValidating = 10,
        statusUnknownData = 11,
        statusErrorValidating = 19,
        statusWaitingConfirmation = 20,
        statusInserting = 30,
        statusErrorInserting = 39,
        statusDone = 99,

        updateProgressBar = function updateProgressBar(bar, progress) {
            bar.removeClass('hidden');
            bar.find('.progress-bar')
                .css('width', progress + '%')
                .attr('aria-valuenow', progress)
                .find('.sr-only').html(progress + '% Complete');

            if (progress == 100) {
                bar.find('.progress-bar')
                    .removeClass('progress-bar-striped active')
                    .addClass('progress-bar-success');
            }
        },

        disableRow = function disableRow(row) {
            row.off('click', '.add-button').off('click', '.map-button');
            row.find('button').addClass('disabled').blur();
            row.find('select').prop('disabled', true);
            row.css('opacity', 0.5);

            if ($('#unknowns').find('button').not('.disabled').length == 0) {
                $('#resume-button').removeClass('disabled');
            }
        },

        ShowLoading = function ShowLoading(row) {
            row.find('button:focus').blur();
            row.LoadingOverlay("show", {image: "", fontawesome: "fa fa-spinner fa-spin", zIndex: 10000});
        },

        HideLoading = function HideLoading(row) {
            row.LoadingOverlay("hide");
        },

        AddUnknown = function AddUnknow(e) {
            var row = $(e.target).parent('.unknown.row'),
                name = row.find('p').text();

            console.log('Calling ' + $(e.target).data('apiurl') + ' to add ' + name);
            ShowLoading(row);
            $.post({
                url     : $(e.target).data('apiurl'),
                data    : {
                    name     : name,
                    job      : jobId,
                    api_token: apiToken
                },
                dataType: 'json'
            }).done(function (data) {
                if (data.success) {
                    disableRow(row);
                }
            }).always(function () {
                HideLoading(row);
            });
        },

        MapUnknown = function MapUnknow(e) {
            var row = $(e.target).parent('.unknown.row'),
                name = row.find('p').text(),
                option = row.find('select').find('option:selected').text();

            console.log('Calling ' + $(e.target).data('apiurl') + ' to map ' + name + ' to ' + option);
            ShowLoading(row);
            $.post({
                url     : $(e.target).data('apiurl'),
                data    : {
                    name     : name,
                    newName  : option,
                    job      : jobId,
                    api_token: apiToken
                },
                dataType: 'json'
            }).done(function (data) {
                if (data.success) {
                    disableRow(row);
                }
            }).always(function () {
                HideLoading(row);
            });
        },

        populateCurrentFixture = function populateCurrentFixture(element, fixture) {
            element.find('#fixture-division').text(fixture.Division);
            element.find('#fixture-match-number').text(fixture.MatchNumber);
            element.find('#fixture-home-team').text(fixture.HomeTeam);
            element.find('#fixture-away-team').text(fixture.AwayTeam);
            element.find('#fixture-date').text(fixture.Date);
            element.find('#fixture-warm-up-time').text(fixture.WarmUpTime);
            element.find('#fixture-start-time').text(fixture.StartTime);
            element.find('#fixture-venue').text(fixture.Venue);
        },

        createUnknownRow = function createUnknownRow(tmplId, text, map) {
            var unknown = $('#' + tmplId).clone(true).removeClass('hidden').attr('id', ''),
                // addButton = unknown.find('.add-button'),
                mapButton = unknown.find('.map-button');

            unknown.find('p').text(text);
            /*
            if (map.ApiUrls.Add) {
                addButton.data('apiurl', map.ApiUrls.Add);
                addButton.on('click', AddUnknown);
            } else {
                addButton.addClass('disabled').blur();
            }
             */

            if (map.ApiUrls.Map) {
                var select = unknown.find('select');
                $.each(map.Mapping, function (index, option) {
                    var $option = $("<option></option>")
                        .attr("value", option.value)
                        .text(option.text);
                    select.append($option);
                });
                mapButton.data('apiurl', map.ApiUrls.Map);
                mapButton.on('click', MapUnknown);
            } else {
                mapButton.addClass('disabled').blur();
            }

            return unknown;
        },

        poll = function poll() {
            $.get({
                url     : '/api/v1/uploads/status.json',
                data    : {
                    job      : jobId,
                    api_token: apiToken
                },
                dataType: 'json'
            }).done(function (data) {
                if (lastTimestamp == data.Timestamp) {
                    setTimeout(poll, 500);
                    return;
                }
                lastTimestamp = data.Timestamp;

                if (data.Error) {
                    $('#message').html(data.Message);
                    return;
                }
                var status = data.Status;

                if (status.StatusCode >= statusValidating) {
                    var validatingProgress = status.StatusCode < (statusValidating + 10) ? status.Progress : 100;
                    updateProgressBar($('#validating-progress'), validatingProgress);
                }
                if (status.StatusCode >= statusInserting) {
                    var insertingProgress = status.StatusCode < (statusInserting + 10) ? status.Progress : 100;
                    updateProgressBar($('#inserting-progress'), insertingProgress);
                }

                if (status.StatusCode === statusUnknownData) {
                    userActionModal.find('.modal-title').text(status.StatusMessage);

                    populateCurrentFixture(userActionModal, status.Fixture);

                    userActionModal.find('#unknowns').empty();
                    $.each(status.Unknowns, function (field, map) {
                        var newUnknown = createUnknownRow('unknown-data-template', status.Fixture[field], map);
                        userActionModal.find('#unknowns').append(newUnknown);
                    });

                    $('#resume-button').addClass('disabled').blur();
                    userActionModal.modal('show');

                } else if (status.StatusCode === statusWaitingConfirmation) {
                    // create confirmation pop up
                    userConfirmationModal.modal('show');
                    // if user click on Proceed
                    // call to resume
                    // else
                    // call clean-up
                } else if (status.StatusCode === statusErrorValidating || status.StatusCode === statusErrorInserting) {
                    var alert = $('#unrecoverable-errors');
                    if (status.ErrorLine) {
                        var errorLine = alert.find('#error-line-number');

                        errorLine.find('span').text(status.ErrorLine);
                        errorLine.removeClass('hidden');
                    }
                    $.each(status.Errors, function (key, error) {
                        alert.find('ul').append($('<li>' + error + '</li>'))
                    });
                    alert.removeClass('hidden');
                    if (status.StatusCode === statusErrorValidating) {
                        $('#validating-progress .progress-bar').removeClass('progress-bar-striped active').addClass('progress-bar-danger');
                    } else if (status.StatusCode === statusErrorInserting) {
                        $('#inserting-progress .progress-bar').removeClass('progress-bar-striped active').addClass('progress-bar-danger');
                    }
                } else if (status.StatusCode !== statusDone) {
                    setTimeout(poll, 500);
                }

            });
        },

        resume = function restart() {
            // Restart the uploading
            $.get({
                url  : '/api/v1/uploads/resume',
                data : {
                    job      : jobId,
                    api_token: apiToken
                },
                async: true
            });
        },

        abandon = function abandon() {
            // Abandon the uploading
            $.get({
                url : '/api/v1/uploads/abandon',
                data: {
                    job      : jobId,
                    api_token: apiToken
                }
            });
            location.href = '/admin/data-management/upload/fixtures';
        };

    resume();
    poll();

    $('.modal').modal({background: false, keyboard: false, show: false});
    $('.modal').on('hidden.bs.modal', function () {
        setTimeout(poll, 500);
    });

    $('#resume-button').on('click', function () {
        resume();
    });
    $('#abandon-button').on('click', function (event) {
        abandon();
    });
    $('#continue-button').on('click', function (event) {
        resume();
    });
})(jQuery);