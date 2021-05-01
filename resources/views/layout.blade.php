<!DOCTYPE html>
<html lang="ru">
<head>
    @section('head')
        <meta charset="UTF-8">
        <title>@yield('title')</title>

        <link href="../css/bootstrap.css" rel="stylesheet">
        <link href="../fontawesome/css/fontawesome.css" rel="stylesheet">
        <link href="../fontawesome/css/all.css" rel="stylesheet">
        <link href="..//css/bootstrap-grid.css" rel="stylesheet">
        <link href="../css/style.css" rel="stylesheet">
    @show
        <style>
            .bd-placeholder-img {
                font-size: 1.125rem;
                text-anchor: middle;
                -webkit-user-select: none;
                -moz-user-select: none;
                user-select: none;
            }

            @media (min-width: 768px) {
                .bd-placeholder-img-lg {
                    font-size: 3.5rem;
                }
            }
        </style>

</head>
<body>
    <div class="section py-4">
      <x-header></x-header>
      <main @yield('class')>
            @yield('page-content')
      </main>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../fontawesome/js/fontawesome.js"></script>
    <x-footer></x-footer>
    @yield('scripts')
</body>
</html>

