@extends('layouts.app', ['title' => 'Login POS'])

@section('content')
<div style="min-height:100vh; display:grid; place-items:center; padding:24px;">
    <div class="panel" style="max-width:430px; width:100%;">
        <div class="badge">Secure POS Access</div>
        <h1 style="font-size:2rem; margin-bottom:10px;">Masuk ke TokoApp POS</h1>
        <p class="muted">Gunakan akun admin atau kasir untuk mulai transaksi dan mengelola data toko.</p>
        <form action="{{ route('login.store') }}" method="POST" style="margin-top:24px; display:grid; gap:16px;">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', 'admin@toko.test') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" value="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <label style="display:flex; align-items:center; gap:10px;">
                <input type="checkbox" name="remember" value="1" style="width:auto;">
                <span>Ingat sesi login</span>
            </label>
            <button class="btn btn-primary" type="submit">Masuk</button>
        </form>
        <div style="margin-top:20px;" class="muted">
            Demo admin: <strong>admin@toko.test</strong> / <strong>password</strong><br>
            Demo kasir: <strong>kasir@toko.test</strong> / <strong>password</strong>
        </div>
    </div>
</div>
@endsection
