@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Seasons') }}</h1></div>
            <div><a href="{{ route('seasons.create') }}"
                    class="btn btn-primary btn-sm">{{ __('New season') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($seasons->isEmpty())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                <p class="mb-0">{{ __('There are no seasons yet.') }}</p>
            </div>
            @else
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Season') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($seasons as $season)
                    <tr dusk={{ "season-{$season->getId()}-row" }}>
                        <td>
                            @component('components.crud.view-button')
                                {{ route('competitions.index', ['season_id' => $season->getId()]) }}
                            @endcomponent
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
            @endif
        </div>
    </div>
@endsection

