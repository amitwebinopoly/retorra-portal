<?php

namespace App\Http\Middleware;

use App\Models\User;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response    {
        /*$guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);*/

        if(isset(Auth::user()->id)){
            $User=new User();
            $auth_user_role=$User->select_fields_by_id(Auth::user()->id, 'role');
            if( (isset($auth_user_role[0]->role))&&(!empty($auth_user_role[0]->role)) ){
                $role = $auth_user_role[0]->role;
                if( ($role=='Admin' || $role=='Designer' || $role=='Showroom')){
                    return $next($request);
                }else{
                    Auth::logout();
                    Session::put('SUCCESS', 'FALSE');
                    Session::put('MESSAGE', 'Access denied. Please Login.');
                    return Redirect::route('fe_home');
                }
            }else{
                Auth::logout();
                Session::put('SUCCESS', 'FALSE');
                Session::put('MESSAGE', 'Access denied. Please Login.');
                return Redirect::route('fe_home');
            }
        }else{
            Session::put('SUCCESS', 'FALSE');
            Session::put('MESSAGE', 'Access denied. Please Login.');
            return Redirect::route('fe_home');
        }

    }
}
