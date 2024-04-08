<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ env('APP_URL')}}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', '' )" />	
	<meta name="keywords" content="@yield('meta_keywords', '') )">
    <!-- Add Custom Meta -->
    @yield('meta')
    <!--  -->
    <link rel="shortcut icon" href="/assets/images/favicon.png" />

	<title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Custom styles for start -->
    @yield('css-start')
    <!--  -->

    <!-- Global theme styles -->
    @include('nobel.includes.common-styles')
    <!--  -->

    <!-- Custom styles for end -->
    @yield('css-end')
    <!--  -->

</head>
<body>
	<div class="main-wrapper">
    <!-- partial:sidebar -->
	@include('nobel.partials.sidebar')
    <!-- partial:sidebar -->
		<div class="page-wrapper">
            <!-- partial:navbar -->
            @include('nobel.partials.navbar')
            <!-- partial:navbar -->
			<div class="page-content">
                @yield('content')
            </div>

			<!-- partial:footer -->
			@include('nobel.partials.footer')
			<!-- partial -->
		
		</div>
	</div>
    <!-- Custom scripts for start -->
    @stack('js-start')
    <!--  -->

    <!-- Global theme styles -->
    @include('nobel.includes.common-scripts')
    <!--  -->

    <!-- Custom scripts for end -->
    @stack('js-end')
    <!--  -->

</body>
</html>    