<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LocaleController extends Controller
{
    /**
     * Switch the application locale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $supportedLocales = config('app.supported_locales', ['en']);
        $localeRule = 'required|string|in:' . implode(',', $supportedLocales);
        
        $request->validate([
            'locale' => $localeRule,
        ]);
        
        // Сохраняем выбранный язык в сессию
        Session::put('locale', $request->locale);
        
        // Перенаправляем обратно на предыдущую страницу
        return Redirect::back();
    }
}