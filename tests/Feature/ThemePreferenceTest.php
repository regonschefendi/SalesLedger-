<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_update_their_theme_preference(): void
    {
        $sales = User::factory()->create([
            'role' => 'sales',
            'theme' => 'light',
        ]);

        $this->actingAs($sales)
            ->postJson('/profile/theme', ['theme' => 'dark'])
            ->assertOk()
            ->assertJson(['success' => true, 'theme' => 'dark']);

        $this->assertSame('dark', $sales->fresh()->theme);
    }

    public function test_admin_can_update_their_theme_preference(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_code' => 'ADMIN1',
            'theme' => 'light',
        ]);

        $this->actingAs($admin)
            ->postJson('/profile/theme', ['theme' => 'dark'])
            ->assertOk();

        $this->assertSame('dark', $admin->fresh()->theme);
    }

    public function test_invalid_theme_value_is_rejected(): void
    {
        $sales = User::factory()->create(['role' => 'sales']);

        $this->actingAs($sales)
            ->postJson('/profile/theme', ['theme' => 'neon'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['theme']);
    }

    public function test_guests_cannot_update_theme_preference(): void
    {
        $this->postJson('/profile/theme', ['theme' => 'dark'])
            ->assertStatus(401);
    }

    public function test_saved_theme_is_applied_when_the_profile_page_renders(): void
    {
        $sales = User::factory()->create([
            'role' => 'sales',
            'theme' => 'dark',
        ]);

        $this->actingAs($sales)
            ->get('/sales/profile')
            ->assertOk()
            ->assertSee('var preference = "dark";', false)
            ->assertSee('data-theme-option="light"', false)
            ->assertSee('data-theme-option="dark"', false);
    }

    public function test_theme_toggle_is_available_on_the_admin_profile_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_code' => 'ADMIN1',
            'theme' => 'dark',
        ]);

        $this->actingAs($admin)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSee('var preference = "dark";', false)
            ->assertSee('data-theme-option="dark"', false)
            ->assertSee('Tema Tampilan');
    }
}
