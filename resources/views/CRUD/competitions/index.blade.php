@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Competitions for season :season', ['season' => $season->getName()]) }}</h1></div>
            <div><a href="{{ route('competitions.create', ['season_id' => $season->getId()]) }}"
                    class="btn btn-primary btn-sm">{{ __('New competition') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Competition') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($competitions as $competition)
                    <tr>
                        <td>
                            @component('components.crud.update-button')
                                {{ route('competitions.edit', [$competition]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('competitions.destroy', [$competition]) }}
                            @endcomponent
                            <span class="pl-2">{{ $competition->getName() }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

