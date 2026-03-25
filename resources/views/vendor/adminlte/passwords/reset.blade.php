@extends('adminlte::master')

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
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

                {{-- Password reset form --}}
                <form action="{{ $password_reset_url }}" method="post">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

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

                    {{-- Password field --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ trans('adminlte::adminlte.password') }}">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Confirm password field --}}
                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation"
                               class="form-control @error('password_confirmation') is-invalid @enderror"
                               placeholder="{{ trans('adminlte::adminlte.retype_password') }}">

                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>

                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- Reset password button --}}
                    <button type="submit" class="btn btn-primary btn-block">
                        {{ trans('adminlte::adminlte.reset_password') }}
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
