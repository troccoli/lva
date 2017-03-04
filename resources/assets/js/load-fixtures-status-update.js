/**
 * Created by Giulio Troccoli-Allard <giulio@troccoli.it> on 10/09/2016.
 */
(function ($) {
    "use strict";

    var apiToken = $('#api_token').val(),
        jobId = $('#job_id').val(),
        lastTimestamp = 0,

        disableRow = function disableRow(row) {
            row.off('click', '.add-button').off('click', '.map-button');
            row.find('button').addClass('disabled').blur();
            row.find('select').prop('disabled', true);

            if ($('#unknowns').find('button').not('.disabled').length == 0) {
                $('#continue-button').removeClass('disabled');
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
                addButton = unknown.find('.add-button'),
                mapButton = unknown.find('.map-button');

            unknown.find('p').text(text);
            if (map.ApiUrls.Add) {
                addButton.data('apiurl', map.ApiUrls.Add);
                addButton.on('click', AddUnknown);
            } else {
                addButton.addClass('disabled').blur();
            }

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
                    setTimeout(poll, 1000);
                    console.log('Same timestamp - nothing to do');
                    return;
                }
                lastTimestamp = data.Timestamp;

                if (data.Error) {
                    $('#message').html(data.Message);
                    return;
                }
                var status = data.Status;
                $('.progress-bar')
                    .css('width', status.Progress + '%')
                    .attr('aria-valuenow', status.Progress)
                    .find('.sr-only').html(status.Progress + '% Complete');

                if (status.StatusCode == 10) {
                    $('#load-fixture-modal').find('.modal-title').text(status.StatusMessage);

                    populateCurrentFixture($('#load-fixture-modal'), status.Fixture);

                    $('#load-fixture-modal').find('#unknowns').empty();
                    $.each(status.Unknowns, function (field, map) {
                        var newUnknown = createUnknownRow('unknown-data-template', status.Fixture[field], map);
                        $('#load-fixture-modal').find('#unknowns').append(newUnknown);
                    });

                    $('#continue-button').addClass('disabled');
                    $('#load-fixture-modal').modal('show');

                } else if (status.StatusCode != 99) {
                    setTimeout(poll, 1000);
                }

            });
        };

    poll();
    $('#load-fixture-modal').modal({show: false});
    $('#load-fixture-modal').on('hidden.bs.modal', function () {
        setTimeout(poll, 1000);
        // Restart the uploading
        $.get({
            url  : '/api/v1/uploads/resume',
            data : {
                job      : jobId,
                api_token: apiToken
            },
            async: true
        });
    });
})(jQuery);