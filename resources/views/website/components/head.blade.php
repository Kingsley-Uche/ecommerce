<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Slayshaper</title>
<meta name="author" content="SlayShaper">
<meta name="robots" content="index, follow">
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">
<meta name="theme-color" content="#000000">
<meta name="application-name" content="SlayShaper">

<meta property="og:type" content="website">
<meta property="og:site_name" content="SlayShaper">
<meta property="og:title" content="SlayShaper | Premium Fashion Store in Nigeria with Worldwide Delivery">
<meta property="og:description" content="Discover premium fashion, stylish clothing, shoes, bags, accessories, and beauty essentials. Shop confidently from Nigeria with worldwide delivery.">
<meta property="og:image" content="{{ asset('assets/images/logo.png') }}">
<meta property="og:url" content="{{ url()->current() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="SlayShaper | Premium Fashion Store">
<meta name="twitter:description" content="Shop trendy fashion, clothing, shoes, bags, and accessories with worldwide delivery from Nigeria.">
<meta name="twitter:image" content="{{ asset('assets/images/logo.png') }}">
  <!-- Favicons -->
  <link href="{{ asset('assets/images/logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/drift-zoom/drift-basic.css') }}" rel="stylesheet">
  <!-- Main CSS File -->
  <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
   <style>
    #loadingSpinner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255,255,255,0.6);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #loadingSpinner .spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #ddd;
        border-top: 5px solid #000;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

</head>