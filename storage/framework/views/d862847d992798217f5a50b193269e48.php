<?php
    $currentLocale = $locale ?? app()->getLocale();
    $languages = [
        'en' => ['flag' => '🇺🇸', 'name' => __('filament.english')],
        'ru' => ['flag' => '🇷🇺', 'name' => __('filament.russian')],
    ];
?>

<div class="relative inline-block">
    <select wire:change="switchLanguage($event.target.value)"
            class="form-select block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white cursor-pointer hover:bg-gray-50 transition-colors duration-200">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
            <option value="<?php echo e($code); ?>" <?php echo e($currentLocale === $code ? 'selected' : ''); ?>>
                <?php echo e($lang['flag']); ?> <?php echo e($lang['name']); ?>

            </option>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </select>
</div><?php /**PATH /var/www/html/resources/views/filament/livewire/language-switcher.blade.php ENDPATH**/ ?>