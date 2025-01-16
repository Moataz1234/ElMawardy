<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class AsgardeoAuthController extends Controller
{
    protected $authorizeUrl;
    protected $tokenUrl;
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scopes = ['openid', 'profile', 'email'];

    public function __construct()
    {
        $this->clientId = env('ASGARDEO_CLIENT_ID');
        $this->clientSecret = env('ASGARDEO_CLIENT_SECRET');
        $this->authorizeUrl = env('ASGARDEO_AUTHORIZE_URL');
        $this->tokenUrl = env('ASGARDEO_TOKEN_URL');
        $this->redirectUri = config('services.asgardeo.redirect'); // Use the dynamically set redirect URI
    }

    /**
     * Redirect the user to Asgardeo's authorization page.
     */
    public function redirectToAsgardeo(Request $request)
    {
        // Get the request's host (IP address)
        $host = $request->getHost();
    
        // Dynamically set the redirect URI based on the host
        $redirectUri = ($host === '192.168.10.178') 
            ? 'http://192.168.10.178:8001/callback' 
            : 'http://172.29.206.251:8001/callback';
    
        $state = Str::random(40);
        session(['asgardeo_oauth_state' => $state]);
    
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri, // Use the dynamically set redirect URI
            'response_type' => 'code',
            'scope' => implode(' ', $this->scopes),
            'state' => $state
        ];
    
        return redirect($this->authorizeUrl . '?' . http_build_query($params));
    }

    /**
     * Handle the callback from Asgardeo.
     */
    public function handleAsgardeoCallback(Request $request)
    {
        $state = $request->query('state');
        $sessionState = session('asgardeo_oauth_state');

        if (!$sessionState || $state !== $sessionState) {
            Log::error('Invalid state.');
            return redirect('/login')->withErrors('Invalid state.');
        }

        session()->forget('asgardeo_oauth_state');

        $code = $request->query('code');

        if (!$code) {
            Log::error('Authorization code not provided.');
            return redirect('/login')->withErrors('Authorization code not provided.');
        }

        // Exchange authorization code for access token
        $response = Http::asForm()->post($this->tokenUrl, [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri, // Use the dynamically set redirect URI
            'code' => $code,
        ]);

        $tokenData = $response->json();

        if (isset($tokenData['error'])) {
            Log::error('Failed to authenticate:', $tokenData);
            return redirect('/login')->withErrors('Failed to authenticate.');
        }

        // Get user info from the token
        $accessToken = $tokenData['access_token'];
        $userInfo = $this->getUserInfo($accessToken);

        Log::info('User Info Response:', $userInfo);
        Log::info('Token Data: ', $tokenData);

        if (!$userInfo || (!isset($userInfo['email']) && !isset($userInfo['sub']))) {
            Log::error('Failed to retrieve user info or email missing', ['userInfo' => $userInfo]);
            return redirect('/login')->withErrors('Failed to retrieve user information.');
        }

        $email = $userInfo['email'] ?? $userInfo['sub'] . '@example.com'; // Use 'sub' as a fallback for email
        $name = $userInfo['name'] ?? 'No Name';

        // Check if the user exists or create a new one
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(Str::random(16)), // Random password, since we're using Asgardeo
                'usertype' => 'user' // Ensure this is set
            ]);

            Auth::login($newUser);
        }

        // Redirect to the intended URL (e.g., /dashboard)
        return redirect()->intended('/dashboard');
    }

    /**
     * Get the authenticated user's info from Asgardeo.
     */
    protected function getUserInfo($accessToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->get('https://api.asgardeo.io/t/elmawardyjewelry/oauth2/userinfo');

        return $response->json();
    }

    public function logout(Request $request)
    {
        try {
            // Get the logout URL from environment
            $asgardeoLogoutUrl = env('ASGARDEO_LOGOUT_URL', 
                'https://api.asgardeo.io/t/elmawardyjewelry/oidc/logout');

            // Use the dynamically set app URL for post-logout redirect
            $postLogoutRedirectUri = config('app.url') . '/login';

            // Log the logout attempt
            Log::info('User logout initiated', [
                'user_id' => Auth::id(),
                'email' => optional(Auth::user())->email
            ]);

            // Perform Laravel logout
            Auth::logout();

            // Clear and invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Build the Asgardeo logout URL with redirect
            $logoutUrl = $asgardeoLogoutUrl . '?' . http_build_query([
                'post_logout_redirect_uri' => $postLogoutRedirectUri,
                'client_id' => env('ASGARDEO_CLIENT_ID')
            ]);

            Log::info('Redirecting to Asgardeo logout', [
                'logout_url' => $logoutUrl
            ]);

            return redirect($logoutUrl);

        } catch (Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // If logout fails, at least logout locally
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')
                ->withErrors('Logout failed: ' . $e->getMessage());
        }
    }
}