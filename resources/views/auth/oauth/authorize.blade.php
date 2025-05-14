<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Authorize Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        .container { max-width: 600px; margin: auto; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        .scopes { margin: 1rem 0; }
        .scopes ul { list-style: none; padding: 0; }
        .scopes li { background: #f4f4f4; padding: 0.5rem; margin-bottom: 0.5rem; border-radius: 4px; }
        form { display: inline-block; margin-right: 1rem; }
        button { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        .approve { background: #4CAF50; color: white; }
        .deny { background: #f44336; color: white; }
    </style>
</head>
<body>
<div class="container">
    <h1>Authorize Application</h1>

    <p><strong>{{ $client->name }}</strong> is requesting permission to access your account.</p>

    @if (count($scopes) > 0)
        <div class="scopes">
            <p>This application will be able to:</p>
            <ul>
                @foreach ($scopes as $scope)
                    <li>{{ $scope->description }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <p>This application is requesting access to your account without any specific permissions.</p>
    @endif

    <!-- Approve Form -->
    <form method="POST" action="{{ route('passport.authorizations.approve') }}">
        @csrf
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->id }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit" class="approve">Authorize</button>
    </form>

    <!-- Deny Form -->
    <form method="POST" action="{{ route('passport.authorizations.deny') }}">
        @csrf
        @method('DELETE')
        <input type="hidden" name="state" value="{{ $request->state }}">
        <input type="hidden" name="client_id" value="{{ $client->id }}">
        <input type="hidden" name="auth_token" value="{{ $authToken }}">
        <button type="submit" class="deny">Cancel</button>
    </form>
</div>
</body>
</html>