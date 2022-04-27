@php($title = $user->login)

@extends('layouts.main')

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">GameAP</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('users.users') }}</a></li>
        <li class="breadcrumb-item active">{{ $user->login }}</li>
    </ol>
@endsection

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered detail-view">
                <tbody>
                    <tr>
                        <th>{{ __('users.login') }}</th>
                        <td>{{ $user->login }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.name') }}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('users.roles') }}</th>
                        <td>{!! $user->roles->implode('name', ', ') !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection