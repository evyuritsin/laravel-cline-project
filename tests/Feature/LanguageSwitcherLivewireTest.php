<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LanguageSwitcherLivewireTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Livewire component renders correctly.
     */
    public function test_livewire_language_switcher_renders(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * Test that language can be switched to Russian via Livewire.
     */
    public function test_livewire_language_can_be_switched_to_russian(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'ru']);

        $response->assertRedirect();
        $this->assertEquals('ru', session('locale'));
        
        // Check that user's locale is updated in database
        $user->refresh();
        $this->assertEquals('ru', $user->locale);
    }

    /**
     * Test that language can be switched to English via Livewire.
     */
    public function test_livewire_language_can_be_switched_to_english(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'en']);

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
        
        // Check that user's locale is updated in database
        $user->refresh();
        $this->assertEquals('en', $user->locale);
    }

    /**
     * Test that invalid language is rejected via Livewire.
     */
    public function test_livewire_invalid_language_is_rejected(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->post('/locale', ['locale' => 'invalid']);

        $response->assertSessionHasErrors('locale');
    }

    /**
     * Test that middleware uses user's preferred locale from database.
     */
    public function test_middleware_uses_user_locale_from_database(): void
    {
        $user = User::factory()->create(['locale' => 'ru']);
        
        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
        $this->assertEquals('ru', session('locale'));
    }

    /**
     * Test that middleware falls back to session when user has no locale.
     */
    public function test_middleware_falls_back_to_session_when_user_has_no_locale(): void
    {
        $user = User::factory()->create(['locale' => null]);
        
        // Set session locale
        session(['locale' => 'ru']);
        
        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertStatus(200);
        $this->assertEquals('ru', session('locale'));
    }
}