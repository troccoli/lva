@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Competitions in the :season season', ['season' => $season->getName()]) }}</h1></div>
            <div><a href="{{ route('competitions.create', [$season]) }}"
                    class="btn btn-primary btn-sm">{{ __('New competition') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($competitions->isEmpty())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                <p class="mb-0">{{ __('There are no competitions in this season yet.') }}</p>
            </div>
            @else
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Competition') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($competitions as $competition)
                    <tr dusk={{ "competition-{$competition->getId()}-row" }}>
                        <td>
                            @component('components.crud.view-button')
                                {{ route('divisions.index', ['competition_id' => $competition->getId()]) }}
                            @endcomponent
                            @component('components.crud.update-button')
                                {{ route('competitions.edit', [$season, $competition]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('competitions.destroy', [$season, $competition]) }}
                            @endcomponent
                            <span class="pl-2">{{ $competition->getName() }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection

