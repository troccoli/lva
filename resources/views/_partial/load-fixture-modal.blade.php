<div id="load-fixture-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Fixture</h3>
                    </div>
                    <div class="panel-body">
                        <span id="fixture-division"></span>-<span id="fixture-match-number"></span>
                        <span id="fixture-home-team"></span>&nbsp;v&nbsp;<span id="fixture-away-team"></span>
                        <br/>
                        on <span id="fixture-date"></span>
                        warm-up: <span id="fixture-warm-up-time"></span>
                        start: <span id="fixture-start-time"></span>
                        <br/>
                        at <span id="fixture-venue"></span>
                    </div>
                </div>
                <p>
                    The following data has not been found in the database. If it's new then add it. If not, then
                    please check the available options and map it to one of the existing.
                </p>
                <h4>What would you like to do?</h4>
                <div id="unknowns" class="container-fluid"></div>
                <div id="unknown-data-template" class="unknown row hidden">
                    <button type="button"
                            class="col-xs-1 add-button btn btn-primary"
                            autocomplete="off"
                            data-apiurl="">
                        Add
                    </button>
                    <p class="col-xs-5"></p>
                    <div class="col-xs-5">
                        <select class="form-control"></select>
                    </div>
                    <button type="button"
                            class="col-xs-1 map-button btn btn-primary"
                            autocomplete="off"
                            data-apiurl="">
                        Map
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button id="continue-button" type="button" class="btn btn-success disabled" data-dismiss="modal">
                    Continue
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->