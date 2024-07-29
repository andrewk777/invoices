<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" href="{{ asset('/images/logo-only-white.png') }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>OADSOFT</title>
  <link rel="stylesheet" type="text/css" href="{{ asset('loader.css') }}" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://demos.pixinvent.com/vuexy-html-admin-template/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&amp;ampdisplay=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/tabler-icons.css"/>
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />

    @vite(['resources/js/main.js'])
</head>

<body>
  <div id="app">

    <div id="loading-bg">
      <div class="loading">
        <div class="effect-1 effects"></div>
        <div class="effect-2 effects"></div>
        <div class="effect-3 effects"></div>
      </div>
    </div>

  </div>

  <script>
    const loaderColor = localStorage.getItem('Invoices-initial-loader-bg') || '#FFFFFF'
    const primaryColor = localStorage.getItem('Invoices-initial-loader-color') || '#7367F0'

    if (loaderColor)
      document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)
    if (loaderColor)
      document.documentElement.style.setProperty('--initial-loader-bg', loaderColor)

    if (primaryColor)
      document.documentElement.style.setProperty('--initial-loader-color', primaryColor)
    </script>
  </body>
</html>
