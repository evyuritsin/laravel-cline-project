<div class="relative inline-block text-left" x-data="{ open: false }">
    <div>
        <button @click="open = !open" class="inline-flex items-center justify-center gap-1 rounded-md text-sm font-medium transition duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-gray-100 p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <span class="hidden sm:inline">{{ strtoupper($locale ?? app()->getLocale()) }}</span>
        </button>
    </div>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-75" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        <div class="py-1">
            <button wire:click="switchLanguage('en')" 
                    class="flex items-center gap-2 w-full px-3 py-2 text-sm hover:bg-gray-100 text-left {{ ($locale ?? app()->getLocale()) === 'en' ? 'bg-gray-100' : '' }}">
                🇺🇸 {{ __('filament.english') }}
            </button>
            <button wire:click="switchLanguage('ru')" 
                    class="flex items-center gap-2 w-full px-3 py-2 text-sm hover:bg-gray-100 text-left {{ ($locale ?? app()->getLocale()) === 'ru' ? 'bg-gray-100' : '' }}">
                🇷🇺 {{ __('filament.russian') }}
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        // Слушаем событие изменения языка
        window.addEventListener('language-changed', (event) => {
            // Обновляем язык приложения без перезагрузки
            fetch(`{{ route('locale.switch') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: `locale=${event.detail.locale}`
            }).then(() => {
                // Обновляем текст на странице без перезагрузки
                window.location.reload();
            });
        });
    });
</script>