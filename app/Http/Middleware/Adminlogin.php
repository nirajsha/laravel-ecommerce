<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class Adminlogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
          if(empty(Session::has('adminSession'))){ //if someone is not logged in but tries to login the admin page then redirect them to login register page
            return redirect('/admin');
        }
        return $next($request);
    }
}
