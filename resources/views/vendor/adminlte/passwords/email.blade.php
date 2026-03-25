@extends('adminlte::master')

@php( $password_email_url = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $password_email_url = $password_email_url ? route($password_email_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $password_email_url = $password_email_url ? url($password_email_url) : '' )
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', 'login-page')

@section('body')
    <div class="login-box">
        {{-- Logo --}}
        <div class="login-logo">
            <a href="{{ $dashboard_url }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" height="50" alt="Logo">
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </a>
        </div>

        {{-- Card Box --}}
        <div class="card card-outline card-primary">
            <div class="card-body">
                <p class="login-box-msg">{{ trans('adminlte::adminlte.password_reset_message') }}</p>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Password reset form --}}
                <form action="{{ $password_email_url }}" method="post">
                    @csrf

                    {{-- Email field --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ $email ?? old('email') }}"
                               placeholder="{{ trans('adminlte::adminlte.email') }}" autofocus>

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Send reset link button --}}
                    <button type="submit" class="btn btn-primary btn-block">
                        {{ trans('adminlte::adminlte.send_password_reset_link') }}
                    </button>
                </form>

                {{-- Login link --}}
                @if(config('adminlte.login_url', 'login'))
                    @php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )

                    @if (config('adminlte.use_route_url', false))
                        @php( $login_url = $login_url ? route($login_url) : '' )
                    @else
                        @php( $login_url = $login_url ? url($login_url) : '' )
                    @endif

                    <p class="mt-3 mb-1">
                        <a href="{{ $login_url }}">
                            {{ trans('adminlte::adminlte.login') }}
                        </a>
                    </p>
                @endif

            </div>
            {{-- /.card-body --}}
        </div>
        {{-- /.card --}}

    </div>
    {{-- /.login-box --}}
@stop

@section('adminlte_js')
    @stack('js')
    @yield('js')
@stop
