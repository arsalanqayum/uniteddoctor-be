<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ env('APP_URL')}}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="@yield('meta_description', '' )" />	
	<meta name="keywords" content="@yield('meta_keywords', '') )">

    <!-- Add Custom Meta -->
    @yield('meta')
    <!--  -->

	<title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Custom styles for start -->
    @yield('css-start')
    <!--  -->

    <!-- Global theme styles -->
    @include('nobel.includes.auth.common-styles')
    <!--  -->

    <!-- Custom styles for end -->
    @yield('css-end')
    <!--  -->

  <link rel="shortcut icon" href="/assets/images/favicon.png" />
</head>
<body>
	<div class="main-wrapper">
		<div class="page-wrapper full-page">
			<div class="page-content d-flex align-items-center justify-content-center">
                @yield('content')
			</div>
		</div>
	</div>

    <!-- Custom scripts for start -->
    @stack('js-start')
    <!--  -->

    <!-- Global theme styles -->
    @include('nobel.includes.auth.common-scripts')
    <!--  -->

    <!-- Custom scripts for end -->
    @stack('js-end')
    <!--  -->

</body>
</html>