@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Clubs') }}</h1></div>
            <div><a href="{{ route('clubs.create') }}"
                    class="btn btn-primary btn-sm">{{ __('New club') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($clubs->isEmpty())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                <p class="mb-0">{{ __('There are no clubs yet.') }}</p>
            </div>
            @else
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Club') }}</th>
                    <th>{{ __('Venue') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($clubs as $club)
                    <tr dusk={{ "club-{$club->getId()}-row" }}>
                        <td>
                            @component('components.crud.view-button')
                                {{ route('teams.index', ['club_id' => $club->getId()]) }}
                            @endcomponent
                            @component('components.crud.update-button')
                                {{ route('clubs.edit', [$club]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('clubs.destroy', [$club]) }}
                            @endcomponent
                            <span class="pl-2">{{ $club->getName() }}</span>
                        </td>
                        <td>{{ $club->getVenue() ? $club->getVenue()->getName() : '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination"> {{ $clubs->links() }} </div>
            @endif
        </div>
    </div>
@endsection

