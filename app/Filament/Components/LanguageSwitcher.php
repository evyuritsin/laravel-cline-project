<?php

namespace App\Filament\Components;

use Filament\Actions\Action;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher
{
    /**
     * Create a language switcher action.
     */
    public static function make(): Action
    {
        return Action::make('language')
            ->label(__('filament.language'))
            ->icon('heroicon-o-language')
            ->color('gray')
            ->form([
                \Filament\Forms\Components\Select::make('locale')
                    ->label(__('filament.language'))
                    ->options([
                        'en' => '🇺🇸 ' . __('filament.english'),
                        'ru' => '🇷🇺 ' . __('filament.russian'),
                    ])
                    ->default(App::getLocale())
                    ->required()
                    ->reactive(),
            ])
            ->action(function (array $data) {
                Session::put('locale', $data['locale']);
                // Перезагружаем страницу для применения нового языка
                return redirect()->back();
            });
    }
}