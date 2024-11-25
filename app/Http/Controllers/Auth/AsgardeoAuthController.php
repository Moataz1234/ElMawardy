<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AsgardeoAuthController extends Controller
{
    protected $authorizeUrl = 'https://asgardeo.io/oauth2/authorize';
    protected $tokenUrl = 'https://asgardeo.io/oauth2/token';
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $scopes = ['openid', 'profile', 'email'];

    public function __construct()
    {
        $this->clientId = env('ASGARDEO_CLIENT_ID');
        $this->clientSecret = env('ASGARDEO_CLIENT_SECRET');
        $this->redirectUri = env('ASGARDEO_REDIRECT_URI');
        $this->authorizeUrl = env('ASGARDEO_AUTHORIZE_URL');
        $this->tokenUrl = env('ASGARDEO_TOKEN_URL');
    }

    /**
     * Redirect the user to Asgardeo's authorization page.
     */
    public function redirectToAsgardeo()
    {
        $state = Str::random(40);
        session(['asgardeo_oauth_state' => $state]);
    
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
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
            'redirect_uri' => $this->redirectUri,
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
}
