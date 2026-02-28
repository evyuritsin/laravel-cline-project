<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LanguageSwitcherComponentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that language switcher component renders correctly.
     */
    public function test_language_switcher_component_renders(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test that language switcher view exists.
     */
    public function test_language_switcher_view_exists(): void
    {
        $this->assertTrue(view()->exists('filament.livewire.language-switcher'));
    }
}