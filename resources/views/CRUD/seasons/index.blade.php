@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Seasons') }}</h1></div>
            <div><a href="{{ route('seasons.create') }}"
                    class="btn btn-primary btn-sm">{{ __('New season') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Season') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($seasons as $season)
                    <tr>
                        <td>
                            @component('components.crud.update-button')
                                {{ route('seasons.edit', [$season]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('seasons.destroy', [$season]) }}
                            @endcomponent
                            <span class="pl-2">{{ $season->getName() }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {{ $seasons->links() }} </div>
        </div>
    </div>
@endsection

