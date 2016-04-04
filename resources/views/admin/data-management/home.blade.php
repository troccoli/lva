@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <a id="seasons-table" href="{{ route('admin.data-management.seasons.index') }}" class="btn btn-primary"><i class="fa fa-table"></i> Seasons</a>
            </div>
        </div>
    </div>
@endsection