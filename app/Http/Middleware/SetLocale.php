<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = null;
        
        // Приоритет: 1. Язык в сессии
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        // Приоритет: 2. Язык в настройках авторизованного пользователя
        elseif (Auth::check() && Auth::user()->locale) {
            $locale = Auth::user()->locale;
            // Сохраняем в сессию для будущих запросов
            Session::put('locale', $locale);
        }
        
        // Устанавливаем язык, если он найден и поддерживается
        if ($locale) {
            $supportedLocales = config('app.supported_locales', ['en']);
            if (in_array($locale, $supportedLocales)) {
                App::setLocale($locale);
            }
        }
        
        return $next($request);
    }
}