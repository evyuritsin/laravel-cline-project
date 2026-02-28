<?php

namespace App\Filament\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public $locale;
    
    public function mount()
    {
        \Log::info('LanguageSwitcher component mounted');
        $this->locale = Session::get('locale', app()->getLocale());
    }
    
    public function switchLanguage($locale)
    {
        \Log::info('switchLanguage called with locale: ' . $locale);
        
        // Проверяем, что язык поддерживается
        $supportedLocales = config('app.supported_locales', ['en']);
        if (!in_array($locale, $supportedLocales)) {
            \Log::warning('Unsupported locale: ' . $locale);
            return;
        }
        
        // Сохраняем язык в сессию
        Session::put('locale', $locale);
        
        // Если пользователь авторизован, сохраняем язык в его настройки
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }
        
        // Устанавливаем язык приложения
        app()->setLocale($locale);
        
        // Перезагружаем страницу для применения языка во всём интерфейсе Filament
        $this->redirect(request()->header('Referer') ?: url()->previous());
    }
    
    public function render()
    {
        return view('filament.livewire.language-switcher');
    }
}