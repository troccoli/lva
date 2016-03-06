@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <a id="seasons-table" href="{{ route('admin::dataManagement::seasons') }}">Seasons</a>
            </div>
        </div>
    </div>
@endsection