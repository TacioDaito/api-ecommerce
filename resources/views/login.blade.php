<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body {
        font-family: system-ui, sans-serif;
        margin: 0;
        min-height: 100vh;
        display: grid;
        place-items: center;
        background: #f5f5f5;
    }
    .card {
        width: 90%;
        max-width: 400px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 2rem;
        margin: 1rem;
    }
    h1 {
        font-size: 1.8rem;
        margin: 0 0 1.5rem 0;
        color: #333;
    }
    input {
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 1rem;
    }
    input:focus {
        outline: 2px solid #4CAF50;
        outline-offset: -1px;
    }
    button {
        width: 100%;
        padding: 0.75rem;
        background: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        font-weight: 600;
        font-size: 1rem;
    }
    button:hover {
        background: #3d8b40;
    }
    .error {
        color: #d32f2f;
        background: #ffebee;
        padding: 0.75rem;
        margin-bottom: 1rem;
        border-radius: 4px;
    }
    @media (max-width: 600px) {
        .card {
            max-width: fit-content;
        }
        h1 {
            font-size: 1.4rem;
        }
        input, button {
            font-size: 0.8rem;
        }
    }
    @media (max-height: 600px) {
        .card {
            max-width: 300px;
        }
        h1 {
            font-size: 1.4rem;
        }
        input, button {
            font-size: 0.8rem;
        }
    }
</style>
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
            @if(request()->getQueryString())
                <input type="hidden" name="redirect_query" value="{{ request()->getQueryString() }}">
            @endif
            <button type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>
