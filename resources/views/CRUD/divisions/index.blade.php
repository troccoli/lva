@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex">
            <div class="mr-auto"><h1>
                    {{ __('Divisions in the :competition :season competition', [
                        'competition' => $competition->getName(),
                        'season' => $competition->getSeason()->getName()
                        ]) }}
            </h1></div>
            <div><a href="{{ route('divisions.create', [$competition]) }}"
                    class="btn btn-primary btn-sm">{{ __('New division') }}</a></div>
        </div>
        <div id="resources-list" class="table" dusk="list">
            @if($divisions->isEmpty())
            <div class="alert alert-warning">
                <h4 class="alert-heading">{{ __('Whoops') }}!</h4>
                <p class="mb-0">{{ __('There are no divisions in this competition yet.') }}</p>
            </div>
            @else
            <table class="table table-bordered table-hover table-sm">
                <thead>
                <tr>
                    <th>{{ __('Divisions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($divisions as $division)
                    <tr>
                        <td>
                            @component('components.crud.update-button')
                                {{ route('divisions.edit', [$competition, $division]) }}
                            @endcomponent
                            @component('components.crud.delete-button')
                                {{ route('divisions.destroy', [$competition, $division]) }}
                            @endcomponent
                            <span class="pl-2">{{ $division->getName() }}</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection

