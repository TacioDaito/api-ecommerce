<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .container { max-width: 600px; margin: auto; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        form { margin-top: 1rem; }
        input[type="email"],
        input[type="password"] {
            display: block;
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background: #4CAF50;
            color: white;
            font-size: 1rem;
        }
        .error {
            color: #f44336;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Login</h1>

    {{-- Display validation errors --}}
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
        @if(request()->getQueryString())
            <input type="hidden" name="redirect_query" value="{{ request()->getQueryString() }}">
        @endif

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
