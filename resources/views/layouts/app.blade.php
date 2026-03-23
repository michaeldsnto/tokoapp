<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'TokoApp POS' }}</title>
    <style>
        :root { --bg:#f4f7fb; --surface:rgba(255,255,255,.88); --surface-strong:#fff; --text:#132238; --muted:#5e7188; --primary:#166534; --primary-soft:#dcfce7; --danger:#dc2626; --warning:#d97706; --border:rgba(19,34,56,.08); --shadow:0 20px 50px rgba(17,24,39,.08); }
        [data-theme="dark"] { --bg:#09111f; --surface:rgba(10,22,40,.86); --surface-strong:#0f1b31; --text:#eef4ff; --muted:#9ab0cb; --primary:#4ade80; --primary-soft:rgba(74,222,128,.12); --danger:#f87171; --warning:#fbbf24; --border:rgba(154,176,203,.12); --shadow:0 24px 60px rgba(0,0,0,.35); }
        * { box-sizing:border-box; } body { margin:0; font-family:"Segoe UI",Tahoma,Geneva,Verdana,sans-serif; color:var(--text); background:radial-gradient(circle at top left, rgba(34,197,94,.18), transparent 32%), radial-gradient(circle at top right, rgba(14,165,233,.12), transparent 28%), var(--bg); }
        a { color:inherit; text-decoration:none; } .shell { display:grid; grid-template-columns:270px minmax(0,1fr); min-height:100vh; } .sidebar { padding:28px; background:linear-gradient(180deg, rgba(9,17,31,.95), rgba(17,24,39,.9)); color:#f8fafc; } .brand { font-size:1.4rem; font-weight:800; letter-spacing:.04em; margin-bottom:6px; } .brand-subtitle { color:rgba(248,250,252,.72); margin-bottom:26px; }
        .nav-link { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; margin-bottom:10px; border-radius:14px; color:rgba(248,250,252,.86); transition:.2s ease; } .nav-link:hover, .nav-link.active { background:rgba(255,255,255,.08); color:#fff; transform:translateX(2px); }
        .sidebar-footer { margin-top:24px; padding:16px; border:1px solid rgba(255,255,255,.08); border-radius:18px; background:rgba(255,255,255,.04); }
        .main { padding:24px; } .topbar { display:flex; flex-wrap:wrap; justify-content:space-between; gap:16px; margin-bottom:24px; } .page-title { font-size:1.9rem; margin:0; } .page-subtitle { margin:6px 0 0; color:var(--muted); } .actions { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
        .panel { background:var(--surface); backdrop-filter:blur(18px); border:1px solid var(--border); border-radius:22px; padding:22px; box-shadow:var(--shadow); } .grid { display:grid; gap:18px; } .grid.cols-2 { grid-template-columns:repeat(2, minmax(0,1fr)); } .grid.cols-3 { grid-template-columns:repeat(3, minmax(0,1fr)); } .grid.cols-4 { grid-template-columns:repeat(4, minmax(0,1fr)); }
        .stat-value { font-size:1.8rem; font-weight:800; margin:12px 0 6px; } .muted { color:var(--muted); } .badge { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:999px; font-size:.85rem; background:var(--primary-soft); color:var(--primary); } .badge.warning { background:rgba(217,119,6,.12); color:var(--warning); }
        .btn { border:none; border-radius:14px; padding:11px 16px; cursor:pointer; font-weight:700; background:var(--surface-strong); color:var(--text); border:1px solid var(--border); } .btn-primary { background:var(--primary); color:#fff; border-color:transparent; } .btn-danger { background:var(--danger); color:#fff; border-color:transparent; } .btn-ghost { background:transparent; } form.inline { display:inline; }
        .table-wrap { overflow-x:auto; } table { width:100%; border-collapse:collapse; } th, td { padding:14px 12px; border-bottom:1px solid var(--border); text-align:left; vertical-align:top; } th { color:var(--muted); font-size:.86rem; text-transform:uppercase; letter-spacing:.05em; }
        .form-grid { display:grid; gap:16px; grid-template-columns:repeat(2, minmax(0,1fr)); } .field { display:flex; flex-direction:column; gap:8px; } .field.full { grid-column:1 / -1; } label { font-weight:700; }
        input, select, textarea { width:100%; padding:12px 14px; border-radius:14px; border:1px solid var(--border); background:var(--surface-strong); color:var(--text); } textarea { min-height:110px; resize:vertical; } .error { color:var(--danger); font-size:.88rem; }
        .flash { margin-bottom:18px; padding:14px 16px; border-radius:16px; border:1px solid rgba(34,197,94,.18); background:rgba(34,197,94,.1); } .product-grid { display:grid; gap:16px; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); }
        .product-card { border:1px solid var(--border); border-radius:18px; background:var(--surface-strong); overflow:hidden; } .product-image { height:150px; background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(14,165,233,.18)); display:grid; place-items:center; font-size:2rem; font-weight:900; } .product-body { padding:16px; }
        .cart-layout { display:grid; gap:18px; grid-template-columns:1.3fr .9fr; align-items:start; } .receipt { max-width:420px; margin:24px auto; padding:24px; background:#fff; color:#111827; border-radius:18px; box-shadow:0 24px 60px rgba(15,23,42,.12); } .receipt table td, .receipt table th { border-bottom:1px dashed #cbd5e1; padding:8px 4px; }
        .pagination { margin-top:18px; }
        @media (max-width:1024px) { .shell { grid-template-columns:1fr; } .sidebar { padding-bottom:10px; } .cart-layout, .grid.cols-4, .grid.cols-3, .grid.cols-2, .form-grid { grid-template-columns:1fr; } }
        @media print { body { background:#fff; } .no-print { display:none !important; } .receipt { box-shadow:none; margin:0; max-width:none; } }
    </style>
</head>
<body>
@auth
<div class="shell">
    <aside class="sidebar no-print">
        <div class="brand">TokoApp POS</div>
        <div class="brand-subtitle">Kasir modern untuk toko sederhana</div>
        <nav>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">POS</a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">Barang</a>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Laporan</a>
            @endif
        </nav>
        <div class="sidebar-footer">
            <div><strong>{{ auth()->user()->name }}</strong></div>
            <div class="muted">{{ auth()->user()->role === 'admin' ? 'Admin' : 'Kasir' }}</div>
            <div style="margin-top:14px; display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" class="btn btn-ghost" id="theme-toggle">Dark mode</button>
                <form action="{{ route('logout') }}" method="POST" class="inline">@csrf<button class="btn btn-danger" type="submit">Logout</button></form>
            </div>
        </div>
    </aside>
    <main class="main">
        <div class="topbar">
            <div>
                <h1 class="page-title">{{ $heading ?? 'Dashboard' }}</h1>
                <p class="page-subtitle">{{ $subheading ?? 'Kelola operasional toko dengan cepat.' }}</p>
            </div>
            <div class="actions">@yield('top_actions')</div>
        </div>
        @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
        @yield('content')
    </main>
</div>
@else
@yield('content')
@endauth
<script>
const storageKey='tokoapp-theme',root=document.documentElement,savedTheme=localStorage.getItem(storageKey); if(savedTheme){root.setAttribute('data-theme',savedTheme)} document.getElementById('theme-toggle')?.addEventListener('click',()=>{const nextTheme=root.getAttribute('data-theme')==='dark'?'light':'dark'; root.setAttribute('data-theme',nextTheme); localStorage.setItem(storageKey,nextTheme);});
</script>
@stack('scripts')
</body>
</html>
