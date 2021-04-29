<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class adminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(session()->has('remember_token')){
            //if remeber token is on db
            $remember_token = session()->get('remember_token');
            $admin = \App\Models\admin::where('remember_token',$remember_token)->first();
            if($admin){
                if($admin->remember_token == $remember_token){
                    return $next($request);
                }else{
                    return redirect('/panel');
                }
            }else{
                return redirect('/panel');
            }
        }else{
            return redirect('/panel');
        }
        return redirect('/');
    }
}
