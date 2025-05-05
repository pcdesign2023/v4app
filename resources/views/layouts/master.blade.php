<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Author" content="Spruko Technologies Private Limited">
    <meta name="Keywords" content="admin,admin dashboard,admin dashboard template,admin panel template,admin template,admin theme,bootstrap 4 admin template,bootstrap 4 dashboard,bootstrap admin,bootstrap admin dashboard,bootstrap admin panel,bootstrap admin template,bootstrap admin theme,bootstrap dashboard,bootstrap form template,bootstrap panel,bootstrap ui kit,dashboard bootstrap 4,dashboard design,dashboard html,dashboard template,dashboard ui kit,envato templates,flat ui,html,html and css templates,html dashboard template,html5,jquery html,premium,premium quality,sidebar bootstrap 4,template admin bootstrap 4"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.head')

    <!-- ✅ Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="main-body app sidebar-mini">
<!-- Loader -->
<div id="global-loader">
    <img src="{{ URL::asset('assets/img/loader.svg') }}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->

@include('layouts.main-sidebar')

<!-- main-content -->
<div class="main-content app-content">
    @include('layouts.main-header')

    <!-- container -->
    <div class="container-fluid">
        @yield('page-header')
        @yield('content')

        @include('layouts.sidebar')
        @include('layouts.models')
        @include('layouts.footer')
    </div>
</div>

<!-- ✅ jQuery (Required for Toastr) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- ✅ Toastr Notification Handling -->
<script>
    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif

    @if(session('success'))
    toastr.success("{{ session('success') }}");
    @endif

    @if(session('info'))
    toastr.info("{{ session('info') }}");
    @endif

    @if(session('warning'))
    toastr.warning("{{ session('warning') }}");
    @endif
</script>

@include('layouts.footer-scripts')
@livewireScripts

</body>

</html>
