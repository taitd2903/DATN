<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; text-align: center; }
        input, button { display: block; width: 100%; margin: 10px 0; padding: 8px; }
    </style>
</head>
<body>
    <h2>@yield('heading')</h2>
    
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color: red;">{{ $errors->first() }}</p>
    @endif

    @yield('content')
</body>
</html>
