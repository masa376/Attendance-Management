<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <script src="https://kit.fontawesome.com/42694f25bf.js"></script>
    <link rel="stylesheet" href="{{ asset('/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/header.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/admin_header.css') }}?v={{ time() }}">
    @yield('css')
</head>

<body>
    @yield('content')
</body>
</html>