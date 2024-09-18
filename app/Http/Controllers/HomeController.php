<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        return view('admin.new-item');
        
    }
    public function checked(){
        if (Auth::check()) {
            // The user is authenticated, access usertype
            $usertype = Auth::user()->usertype;
            // Do something with usertype
        } 
        else {
            // The user is not authenticated, redirect to login
            return redirect()->route('login');
        }
    }
}
