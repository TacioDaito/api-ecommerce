<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
</head>

<body>
<div class="container">
    <div class="card">
        <h1>Login</h1>
        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            {{-- Preserve OAuth2 query string --}}
            @dd (request()->query())
            @if(request()->getQueryString())
                <input type="hidden" name="redirect_query" value="{{ request()->getQueryString() }}">
            @endif
            <button type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
