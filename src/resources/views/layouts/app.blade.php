<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>SNS Study</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family: system-ui, sans-serif; line-height:1.6; padding:24px; max-width:820px; margin:0 auto;}
    .card{border:1px solid #ddd; border-radius:12px; padding:16px; margin:12px 0;}
    .muted{color:#666; font-size:14px;}
    .flash{padding:10px 12px; margin:12px 0; border-radius:4px;}
    .flash.success{background:#e6ffed; color:#036400; border:1px solid #b7ebc0;}
    .flash.error{background:#fff1f0; color:#a8071a; border:1px solid #ffa39e;}
    .error-text{color:#a8071a; font-size:0.9em;}
    a{color:#0b5ed7; text-decoration:none} a:hover{text-decoration:underline}
    .field{margin:12px 0;}
  </style>
</head>
<body>
  <div class="container">
    @if (session('status'))
      <div class="flash success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
      <div class="flash error">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="flash error">
        <ul style="margin:0 0 0 1em;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @yield('content')
  </div>
</body>
</html>
