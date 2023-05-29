<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class usercontroller extends Controller
{
    public function Home(){
        return view('Register');
    }

    public function dashboard(){
        return view('dashboard');
    }

    public function login(){
        return view('login');
    }

    public function loginUser(Request $req){
        $req->validate([
            'email'=>'required',
            'password'=>'required',
        ]);

        if( Auth::attempt($req->only('email','password')) ){
            //If Credential are correct move to board page
            return redirect(route('dashboard'));
        }else{
            return redirect(route('login'))->with('error','Credential are incorrect');
        }
    }
    
    public function register(){
        return view('register');
    }

    public function registerUser(Request $req){
        $req->validate([
            'username'=>'required',
            'email'=>'required|unique:users',
            'password'=>'required|confirmed',
            'password_confirmation'=>'required',
        ]);

 
        User::create([
            'name'=>$req->username,
            'email'=>$req->email,
            'password'=>Hash::make($req->password),
        ]);

        if(Auth::attempt($req->only('email','password'))){
            return redirect(route('dashboard'));
        }

    }   

    public function logout(){
        Auth::logout();
        return redirect(route('login'));
    }
    
        
}