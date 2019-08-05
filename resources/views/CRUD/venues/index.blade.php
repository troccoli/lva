@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>{{ __('Venues') }}</h1></div>
            <div><a href="{{ route('venues.create') }}"
                    class="btn btn-primary btn-sm mt-2">{{ __('New venue') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($venues->isEmpty())
                <div class="alert alert-warning">
                    <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                    <p class="mb-0">{{ __('There are no venues yet.') }}</p>
                </div>
            @else
                <table class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>{{ __('Venue') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($venues as $venue)
                        <tr>
                            <td>
                                @component('components.crud.view-button')
                                    {{ route('venues.show', [$venue]) }}
                                @endcomponent
                                @component('components.crud.update-button')
                                    {{ route('venues.edit', [$venue]) }}
                                @endcomponent
                                @component('components.crud.delete-button')
                                    {{ route('venues.destroy', [$venue]) }}
                                @endcomponent
                                <span class="pl-2">{{ $venue->getName() }}</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination"> {{ $venues->links() }} </div>
            @endif
        </div>
    </div>
@endsection

