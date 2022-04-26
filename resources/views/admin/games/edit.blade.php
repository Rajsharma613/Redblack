@php($title = __('games.title_edit'))

@extends('layouts.main')

@section('breadclumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">GameAP</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.games.index') }}">{{ __('games.games') }}</a></li>
        <li class="breadcrumb-item active">{{ __('games.title_edit') }}</li>
    </ol>
@endsection

@section('content')
    @include('components.form.errors_block')

    <div class="mb-1">
        <a class="btn btn-large btn-success" href="{{ route('admin.game_mods.create', ['game' => $game->code]) }}">
            <span class="fa fa-cat"></span>&nbsp;{{ __('games.add_mod') }}
        </a>
    </div>

    {!! Form::model($game, ['method' => 'PATCH','route' => ['admin.games.update', $game->code]]) !!}

        <div class="row mt-2 mb-2">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        {{ __('games.basic_info') }}
                    </div>
                    <div class="card-body">
                        {{ Form::bsText('code', null, null, ['disabled']) }}
                        {{ Form::bsText('start_code') }}
                        {{ Form::bsText('name') }}
    
                        {{ Form::bsText('engine') }}
                        {{ Form::bsText('engine_version') }}
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        {{ __('games.mods') }}
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($game->mods as $mod)
                                <li class="list-group-item"><a href="{{ route('admin.game_mods.edit', ['game_mod' => $mod->id]) }}">{{ $mod->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
    
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        {{ __('games.steam_info') }}
                    </div>
                    <div class="card-body">
                        {{ Form::bsText('steam_app_id_nix') }}
                        {{ Form::bsText('steam_app_id_win') }}
                        {{ Form::bsText('steam_app_set_config') }}
                    </div>
                </div>
                
                <div class="card mt-2">
                    <div class="card-header">
                        {{ __('games.repositories_local') }}
                    </div>
                    <div class="card-body">
                        {{ Form::bsText('local_repository_nix') }}
                        {{ Form::bsText('local_repository_win') }}
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-header">
                        {{ __('games.repositories_remote') }}
                    </div>
                    <div class="card-body">
                        {{ Form::bsText('remote_repository_nix') }}
                        {{ Form::bsText('remote_repository_win') }}
                    </div>
                </div>
            </div>
        </div>

        {{ Form::submit(__('main.save'), ['class' => 'btn btn-success']) }}

    {!! Form::close() !!}
@endsection
