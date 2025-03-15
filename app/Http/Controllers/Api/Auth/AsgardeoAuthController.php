<?php

namespace App\Http\Controllers\Api\Auth;

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

    public function __construct(Request $request)
    {
        $this->clientId = config('services.asgardeo.client_id');
        $this->clientSecret = config('services.asgardeo.client_secret');
        $this->authorizeUrl = config('services.asgardeo.authorize_url');
        $this->tokenUrl = config('services.asgardeo.token_url');
        $this->redirectUri = 'http://localhost:8000/api/auth/callback';
    }

    /**
     * Get the login URL for Asgardeo
     */
    public function getLoginUrl()
    {
        try {
            $state = Str::random(40);
            
            $params = [
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'response_type' => 'code',
                'scope' => 'openid profile email',
                'state' => $state
            ];

            $loginUrl = $this->authorizeUrl . '?' . http_build_query($params);
            
            Log::info('Generated Login URL:', ['url' => $loginUrl]);

            return response()->json([
                'login_url' => $loginUrl,
                'state' => $state
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating login URL:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to generate login URL',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle the callback from Asgardeo
     */
    public function handleCallback(Request $request)
    {
        try {
            Log::info('Callback received', ['request' => $request->all()]);
            
            $code = $request->input('code');
            
            if (!$code) {
                Log::error('No authorization code provided');
                return response()->json([
                    'error' => 'Authorization code not provided'
                ], 400);
            }

            // Exchange code for token
            $tokenResponse = Http::withoutVerifying()
                ->asForm()
                ->post($this->tokenUrl, [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $this->redirectUri,
                    'code' => $code
                ]);

            if (!$tokenResponse->successful()) {
                Log::error('Token request failed', ['response' => $tokenResponse->json()]);
                return response()->json([
                    'error' => 'Failed to get access token'
                ], 401);
            }

            $tokenData = $tokenResponse->json();
            
            // Get user info
            $userInfo = $this->getUserInfo($tokenData['access_token']);
            
            if (!$userInfo) {
                return response()->json(['error' => 'Failed to get user information'], 401);
            }

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $userInfo['email'] ?? $userInfo['sub']],
                [
                    'name' => $userInfo['name'] ?? 'User',
                    'password' => bcrypt(Str::random(16))
                ]
            );

            Auth::login($user);

            // Return token and user info
            return response()->json([
                'token' => $tokenData['access_token'],
                'user' => $user,
                'redirect' => $this->getRedirectUrl($user)
            ]);
        } catch (Exception $e) {
            Log::error('Callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated user's info from Asgardeo
     */
    protected function getUserInfo($accessToken)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => "Bearer {$accessToken}",
        ])->get(config('services.asgardeo.userinfo_url'));

        return $response->json();
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if ($user) {
                // Revoke all tokens
                $user->tokens()->delete();
            }

            $asgardeoLogoutUrl = config('services.asgardeo.logout_url');
            $postLogoutRedirectUri = config('app.url') . '/login';

            return response()->json([
                'message' => 'Logged out successfully',
                'logout_url' => $asgardeoLogoutUrl . '?' . http_build_query([
                    'post_logout_redirect_uri' => $postLogoutRedirectUri,
                    'client_id' => $this->clientId
                ])
            ]);

        } catch (Exception $e) {
            Log::error('Logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Logout failed'], 500);
        }
    }

    public function handleWebCallback(Request $request)
    {
        try {
            Log::info('Web Callback received', ['request' => $request->all()]);
            
            $code = $request->input('code');
            $state = $request->input('state');
            
            if (!$code) {
                Log::error('No authorization code provided');
                return redirect('http://localhost:3000/login')->with('error', 'Authorization failed');
            }

            // Exchange authorization code for access token
            $tokenResponse = Http::withoutVerifying()
                ->asForm()
                ->post($this->tokenUrl, [
                    'grant_type' => 'authorization_code',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => config('services.asgardeo.redirect'),
                    'code' => $code,
                ]);

            if (!$tokenResponse->successful()) {
                Log::error('Token request failed', ['response' => $tokenResponse->json()]);
                return redirect('http://localhost:3000/login')->with('error', 'Authentication failed');
            }

            $tokenData = $tokenResponse->json();
            
            // Get user info
            $userInfo = $this->getUserInfo($tokenData['access_token']);
            
            if (!$userInfo) {
                return redirect('http://localhost:3000/login')->with('error', 'Failed to get user information');
            }

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $userInfo['email'] ?? $userInfo['sub']],
                [
                    'name' => $userInfo['name'] ?? 'User',
                    'password' => bcrypt(Str::random(16))
                ]
            );

            Auth::login($user);

            // Determine redirect URL based on user type
            $redirectUrl = match($user->usertype) {
                'admin' => '/admin/inventory',
                'rabea' => '/orders/rabea',
                'Acc' => '/sell-requests/acc',
                'user' => '/shop-dashboard',
                default => '/dashboard'
            };

            // Redirect to React app with token and user info
            return redirect("http://localhost:3000/auth-callback?" . http_build_query([
                'token' => $tokenData['access_token'],
                'user' => json_encode($user),
                'redirect' => $redirectUrl
            ]));

        } catch (Exception $e) {
            Log::error('Callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('http://localhost:3000/login')->with('error', 'Authentication failed');
        }
    }

    private function getRedirectUrl($user)
    {
        return match($user->usertype) {
            'admin' => '/admin/inventory',
            'rabea' => '/orders/rabea',
            'Acc' => '/sell-requests/acc',
            'user' => '/shop-dashboard',
            default => '/dashboard'
        };
    }
} 