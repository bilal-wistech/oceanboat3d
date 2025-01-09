<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <base href="{{url('/')}}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Crm') }} | @yield('title', $page_title ?? '')</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />

  <link href="{{asset('assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/plugins/custom/custom-scrollbar/jquery.mCustomScrollbar.min.css')}}" rel="stylesheet" type="text/css" />

  <link href="{{asset('assets/css/themes/layout/header/base/light.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/css/themes/layout/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/css/themes/layout/brand/dark.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/css/themes/layout/aside/dark.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/plugins/custom/jquery-autocomplete/css/jquery.autocomplete.css')}}" rel="stylesheet" type="text/css" />
  <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet" type="text/css" />

  <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
  @stack('css')
  @include('layouts.blocks.cssStyles')
  @livewireStyles
</head>
<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize aside-minimize-hoverable page-loading">
  @include('layouts.blocks.mobileheader')
  <div class="d-flex flex-column flex-root">
    <div class="d-flex flex-row flex-column-fluid page">
      @include('layouts.blocks.sidebar')
      <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
        @include('layouts.blocks.topbar')
        <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
          @include('layouts.blocks.toastr')
          <div class="container">
            @yield('content')
          </div>
        </div>
        @include('layouts.blocks.footer')
      </div>
    </div>
  </div>
  <div id="kt_scrolltop" class="scrolltop">
    <span class="svg-icon">
      <!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <polygon points="0 0 24 0 24 24 0 24" />
          <rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
          <path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
        </g>
      </svg>
      <!--end::Svg Icon-->
    </span>
  </div>

	<script>
    var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#EBEDF3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };
  </script>
  <script src="{{asset('assets/plugins/global/plugins.bundle.js')}}"></script>
  <script src="{{asset('assets/plugins/global/jquery.pjax.js')}}"></script>
  <script src="{{asset('assets/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
  <script src="{{asset('assets/js/scripts.bundle.js')}}"></script>
	<script src="{{asset('assets/js/pages/widgets.js')}}"></script>
  <script src="{{asset('assets/plugins/custom/custom-scrollbar/jquery.mCustomScrollbar.js')}}"></script>
  <script src="{{asset('assets/plugins/custom/sweetalert2/sweetalert2.all.js')}}"></script>
  <script src="{{asset('assets/plugins/custom/jquery-autocomplete/js/jquery.autocomplete.js')}}"></script>
  <!--script src="{{asset('assets/plugins/custom/autoscroll/jquery-ias.min.js')}}"></script-->
  <script src="https://unpkg.com/@webcreate/infinite-ajax-scroll@3.0.0-rc.1/dist/infinite-ajax-scroll.min.js"></script>
  <script src="{{asset('assets/js/custom.js?v=1.0.0.1')}}"></script>

  @stack('js')
  @include('layouts.blocks.jscripts')
  @livewireScripts
</body>
</html>
