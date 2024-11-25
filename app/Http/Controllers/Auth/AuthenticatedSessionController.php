<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
<<<<<<< HEAD
        switch ($request->user()->usertype) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'rabea':
                return redirect()->route('orders.rabea.index');
            default:
                return redirect()->intended(route('gold-items.shop', ['shop' => $request->user()->name]));
=======
        if($request->user()->usertype ==='admin'){
            return redirect('admin/inventory');
>>>>>>> f6b866230d849c5df5c291e82aeecf4c795c326e
        }
    }

    /**
     * Destroy an authenticated session.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     Auth::guard('web')->logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();
    //     // return redirect("/");
    //     return redirect()->away('https://api.asgardeo.io/t/elmawardyjewelry/oidc/logout?redirect_uri=' . urlencode(route('login')));
    // }
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
