{{-- Default Content Wrapper --}}
<div class="content-wrapper bgWeb">

    {{-- Preloader Animation (cwrapper mode) --}}

    {{-- Content Header --}}
    @hasSection('content_header')
        <div class="content-header">
            <div class="container-fluid">
                @yield('content_header')
            </div>
        </div>
    @endif

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

</div>
