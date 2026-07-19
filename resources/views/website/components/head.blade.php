<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Slayshaper</title>

  <!-- Meta Tags -->
  <meta name="author" content="SlayShaper">
  <meta name="robots" content="index, follow">
  <meta name="language" content="English">
  <meta name="theme-color" content="#000000">

  <!-- Open Graph / Twitter -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="SlayShaper">
  <meta property="og:title" content="SlayShaper | Premium Fashion Store in Nigeria with Worldwide Delivery">
  <meta property="og:description" content="Discover premium fashion, stylish clothing, shoes, bags, accessories, and beauty essentials.">
  <meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
  <meta property="og:url" content="{{ url()->current() }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="SlayShaper | Premium Fashion Store">
  <meta name="twitter:description" content="Shop trendy fashion...">
  <meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">

  <!-- Preloads -->
  <link rel="preload" href="{{ asset('assets/css/main.css') }}" as="style">
  <link rel="preload" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" as="style">

  <!-- Favicons -->
  <link href="{{ asset('assets/images/logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Montserrat:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Critical CSS -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">

  <!-- Non-critical CSS (lazy loaded) -->
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet" media="print" onload="this.media='all'">
  <link href="{{ asset('assets/vendor/drift-zoom/drift-basic.css') }}" rel="stylesheet" media="print" onload="this.media='all'">

  <style>
    #loadingSpinner {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%;
      background: rgba(255,255,255,0.7);
      display: none; justify-content: center; align-items: center;
      z-index: 9999;
    }
    #loadingSpinner .spinner {
      width: 50px; height: 50px;
      border: 5px solid #ddd; border-top: 5px solid #000;
      border-radius: 50%; animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>