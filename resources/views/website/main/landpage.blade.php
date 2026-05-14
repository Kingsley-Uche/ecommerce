<!DOCTYPE html>
<html lang="en">
  @include('website.components.head')

<body class="index-page">
  <div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3"></div>

@include('website.components.header') 
@yield('content')
@include('website.components.footer')

</html>