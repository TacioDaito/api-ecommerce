
<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

Route::get('login', [LoginController::class, 'view'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::get('/redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));
 
    $request->session()->put(
        'code_verifier', $codeVerifier = Str::random(128)
    );
 
    $codeChallenge = strtr(rtrim(
        base64_encode(hash('sha256', $codeVerifier, true))
    , '='), '+/', '-_');
 
    $query = http_build_query([
        'client_id' => env('OAUTH_CLIENT_ID'),
        'redirect_uri' => 'http://127.0.0.1:8000/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
        'code_challenge' => $codeChallenge,
        'code_challenge_method' => 'S256',
        // 'prompt' => 'consent', // "none", "consent", or "login"
    ]);
 
    return redirect('http://127.0.0.1:8000/oauth/authorize?'.$query);
})->name('redirect');

 
Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull('state');
    $codeVerifier = $request->session()->pull('code_verifier');
 
    throw_unless(
        strlen($state) > 0 && $state === $request->state,
        InvalidArgumentException::class
    );
    $response = Http::asForm()->post('http://127.0.0.1:8000/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => env('OAUTH_CLIENT_ID'),
        'redirect_uri' => 'http://127.0.0.1:8000/callback',
        'code_verifier' => $codeVerifier,
        'code' => $request->query('code'),
    ]);
    dd($response->json());
    return $response->json();
})->name('callback');