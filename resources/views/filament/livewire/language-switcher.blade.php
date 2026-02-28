@php
    $currentLocale = $locale ?? app()->getLocale();
    $languages = [
        'en' => ['flag' => '🇺🇸', 'name' => __('filament.english')],
        'ru' => ['flag' => '🇷🇺', 'name' => __('filament.russian')],
    ];
@endphp

<div class="relative inline-block">
    <select wire:change="switchLanguage($event.target.value)"
            class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white cursor-pointer hover:bg-gray-50 transition-colors duration-200">
        @foreach($languages as $code => $lang)
            <option value="{{ $code }}" {{ $currentLocale === $code ? 'selected' : '' }}>
                {{ $lang['flag'] }} {{ $lang['name'] }}
            </option>
        @endforeach
    </select>
</div>