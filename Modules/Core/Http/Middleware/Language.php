<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;


class Language{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Make sure current locale exists.
        $locale = $request->get('lang');

        //set and get lang
        if(!isset($locale)){
            $locale = App::getLocale();
        }

        Config::set('locales.lang', $locale);
        return $next($request);
    }

}