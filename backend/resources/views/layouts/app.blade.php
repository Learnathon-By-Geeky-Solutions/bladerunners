<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title','MyApp')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <nav class="navbar navbar-light bg-light mb-4">
    <a class="navbar-brand" href="/">MyApp</a>
  </nav>
  <div class="container">@yield('content')</div>
</body>
</html>
