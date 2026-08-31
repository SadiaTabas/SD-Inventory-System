<!DOCTYPE html>

<html lang="en">

<head>

 
<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>@yield('title', 'My CRUD App')</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])
 

</head>

<body>

 
@include('layouts.navbar')

<main>
    @yield('content')
</main>
 

</body>

</html>
