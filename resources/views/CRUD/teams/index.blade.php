@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Teams in the :club club', ['club' => $club->getName()]) }}</h1></div>
            <div><a href="{{ route('teams.create', [$club]) }}"
                    class="btn btn-primary btn-sm">{{ __('New team') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($teams->isEmpty())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                <p class="mb-0">{{ __('There are no teams in this club yet.') }}</p>
            </div>
            @else
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Team') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($teams as $team)
                    <tr>
                        <td>
                            @component('components.crud.update-button')
                                {{ route('teams.edit', [$club, $team]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('teams.destroy', [$club, $team]) }}
                            @endcomponent
                            <span class="pl-2">{{ $team->getName() }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection

