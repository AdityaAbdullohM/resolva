<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mengarahkan...</title>
    <style>
        html,body{height:100%;margin:0}
        .center{height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg,#f7fafc,#edf2f7);font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial}
        .card{background:#fff;padding:28px;border-radius:14px;box-shadow:0 8px 30px rgba(2,6,23,0.08);text-align:center}
        .spinner{width:48px;height:48px;border-radius:50%;background:linear-gradient(90deg,#06b6d4,#34d399);animation:spin 1s linear infinite;display:inline-block}
        @keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}
        .text{margin-top:12px;color:#0f172a;font-weight:600}
    </style>
</head>
<body>
    <div class="center">
        <div class="card">
            <div class="spinner" aria-hidden="true"></div>
            <div class="text">Masuk... Mengarahkan ke dashboard</div>
        </div>
    </div>

    <script>
        // Immediately navigate to dashboard so the browser can show a UI
        // while the server prepares the full dashboard page.
        (function(){
            var redirectTo = @json($redirect ?? url('/'));
            // Use replace so history isn't cluttered
            window.location.replace(redirectTo);
        })();
    </script>
</body>
</html>