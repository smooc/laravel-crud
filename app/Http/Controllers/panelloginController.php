<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class panelloginController extends Controller
{
    public function index(){
        $users = \App\Models\admin::all();
      
        return view('/panel/users',[
            'users' => $users,
        ]);
    }







    public function inOrOut(){
        if(session()->has('remember_token')){// check if the user has remember_token and if it is valid
            $remember_token = session()->get('remember_token');
            $admin = \App\Models\admin::where('remember_token',$remember_token)->first();
            if($admin){
                if($admin->remember_token == $remember_token){
                    return redirect('panel/settings');
                }else{
                    return view('panel/login');
                }
            }else{
                return view('panel/login');
            }
        }else{
            return view('panel/login');
        }
       
    }

    public function login(){
        $data = request()->validate([
            'username' => '',
            'password' => ''
        ]);
        $admin = \App\Models\admin::where('username',$data['username'])->first();
        if($admin){
            if (Hash::check($data['password'], $admin->password)){
                //create remember token
                // delete the below line while uncommenting the below comment
                $remember_token = $admin->remember_token;
               /* $remember_token = Str::random(60);
                $admin->remember_token = $remember_token;
                $admin->save(); */
                session()->put('remember_token', $remember_token);
                return redirect('/panel/settings');
            }else{
                $response = array(
                    'message' => 'Yanlış Kullanıcı Adı veya Şifre.',
                    'title' => 'Hata',
                    'type' => 'danger'
                );
                return redirect('/panel')->with('response' , $response);
            }
        }else{
            $response = array(
                'message' => 'Yanlış Kullanıcı Adı veya Şifre.',
                'title' => 'Hata',
                'type' => 'danger'
            );
            return redirect('/panel')->with('response' , $response);

         
        }
    }

    public function createPassword(Request $request){
        $password = $request->password;
        $password = Hash::make($password);
        dd($password);
    }

    public function logout(){
        session()->forget('remember_token');
        return redirect('/panel/login');
    }
}
