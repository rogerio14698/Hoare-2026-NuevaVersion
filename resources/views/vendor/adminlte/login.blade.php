@extends('adminlte::master')

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
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
        <div class="login-logo ">
            <a href="{{ $dashboard_url }}">
                <img src="{{ asset(config('adminlte.logo_img')) }}" height="50" alt="Logo">
                {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
            </a>
        </div>

        {{-- Card Box --}}
        <div class="card card-outline login-bodybg">
            <div class="card-body">
                <p class="login-box-msg">{{ trans('adminlte::adminlte.login_message') }}</p>

                {{-- Login form --}}
                <form action="{{ $login_url }}" method="post">
                    @csrf

                    {{-- Email field --}}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="{{ trans('adminlte::adminlte.email') }}" autofocus>

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

                    {{-- Login field --}}
                    <div class="row">
                        <div class="col-7">
                            <div class="icheck-primary remember">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label for="remember" >
                                    {{ trans('adminlte::adminlte.remember_me') }}
                                </label>
                            </div>
                        </div>

                        <div class="col-5">
                            <button type="submit" class="botonLogin btn-block">
                                {{ trans('adminlte::adminlte.sign_in') }}
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Password reset link --}}
                @if(config('adminlte.password_reset_url', 'password/reset'))
                    @php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

                    @if (config('adminlte.use_route_url', false))
                        @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
                    @else
                        @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
                    @endif

                    <p class="mb-1 olvideContraseña">
                        <a href="{{ $password_reset_url }}">
                            {{ trans('adminlte::adminlte.i_forgot_my_password') }}
                        </a>
                    </p>
                @endif

                {{-- Register link --}}
                @if(config('adminlte.register_url', 'register'))
                    @php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

                    @if (config('adminlte.use_route_url', false))
                        @php( $register_url = $register_url ? route($register_url) : '' )
                    @else
                        @php( $register_url = $register_url ? url($register_url) : '' )
                    @endif

                    <p class="mb-0">
                        <a href="{{ $register_url }}" class="text-center">
                            {{ trans('adminlte::adminlte.register_a_new_membership') }}
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
