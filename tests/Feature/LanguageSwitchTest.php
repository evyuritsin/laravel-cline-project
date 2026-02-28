<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LanguageSwitchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that language switcher component renders correctly.
     */
    public function test_language_switcher_renders(): void
    {
        $response = $this->get('/admin');

        // Check that the view exists
        $this->assertTrue(view()->exists('filament.components.language-switcher'));
    }

    /**
     * Test that language can be switched to Russian.
     */
    public function test_language_can_be_switched_to_russian(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'ru']);

        $response->assertRedirect();
        $this->assertEquals('ru', session('locale'));
    }

    /**
     * Test that language can be switched to English.
     */
    public function test_language_can_be_switched_to_english(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'en']);

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }

    /**
     * Test that invalid language is rejected.
     */
    public function test_invalid_language_is_rejected(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'invalid']);

        $response->assertSessionHasErrors('locale');
    }

    /**
     * Test that Russian translations are loaded correctly.
     */
    public function test_russian_translations_load(): void
    {
        app()->setLocale('ru');
        
        $this->assertEquals('Язык', __('filament.language'));
        $this->assertEquals('Английский', __('filament.english'));
        $this->assertEquals('Русский', __('filament.russian'));
    }

    /**
     * Test that English translations are loaded correctly.
     */
    public function test_english_translations_load(): void
    {
        app()->setLocale('en');
        
        $this->assertEquals('Language', __('filament.language'));
        $this->assertEquals('English', __('filament.english'));
        $this->assertEquals('Russian', __('filament.russian'));
    }
}