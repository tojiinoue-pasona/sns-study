<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>SNS Study</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    body{font-family: system-ui, sans-serif; line-height:1.6; padding:24px; max-width:820px; margin:0 auto;}
    .card{border:1px solid #ddd; border-radius:12px; padding:16px; margin:12px 0;}
    .muted{color:#666; font-size:14px;}
    .flash{padding:10px 12px; margin:12px 0; border-radius:4px;}
    .flash.success{background:#e6ffed; color:#036400; border:1px solid #b7ebc0;}
    .flash.error{background:#fff1f0; color:#a8071a; border:1px solid #ffa39e;}
    .error-text{color:#a8071a; font-size:0.9em;}
    .like-btn.liked{color:#d63384;}
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

  <script>
  (() => {
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenEl ? tokenEl.content : '';

    async function toggleLike(btn) {
      const url = btn.dataset.url;
      const postId = btn.dataset.postId;
      btn.disabled = true;
      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
          }
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.message || 'エラーが発生しました');

        const countEl = document.querySelector(`.like-count[data-post-id="${postId}"]`);
        if (countEl) countEl.textContent = data.count ?? 0;

        const liked = !!data.liked;
        btn.classList.toggle('liked', liked);
        btn.textContent = liked ? 'いいね解除' : 'いいね';
      } catch (e) {
        alert(e.message || '通信エラー');
      } finally {
        btn.disabled = false;
      }
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.like-btn');
      if (btn) {
        e.preventDefault();
        toggleLike(btn);
      }
    });
  })();
  </script>
</body>
</html>
