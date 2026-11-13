<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.brevo.key', null);
        Mail::fake();
    }

    private function registerPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'username' => 'budi',
            'email' => 'Budi@Example.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
        ], $overrides);
    }

    private function registerAndGetOtp(string $email = 'budi@example.com'): string
    {
        $this->postJson('/api/register', $this->registerPayload([
            'email' => $email,
            'username' => 'budi',
        ]))->assertOk()->assertJson(['success' => true]);

        $otp = Cache::get('otp_'.$email);

        $this->assertNotNull($otp);

        return (string) $otp;
    }

    public function test_registration_verification_role_selection_and_logout_flow(): void
    {
        $this->postJson('/api/register', $this->registerPayload())
            ->assertOk()
            ->assertJson(['success' => true, 'redirect' => '/verify-email']);

        $this->assertAuthenticated();

        $user = User::where('username', 'budi')->firstOrFail();

        $this->assertSame('budi@example.com', $user->email);
        $this->assertNull($user->email_verified_at);

        $otp = (string) Cache::get('otp_budi@example.com');

        $this->postJson('/api/verify-otp', ['otp' => $otp])
            ->assertOk()
            ->assertJson(['success' => true, 'redirect' => '/select-role']);

        $this->assertNotNull($user->fresh()->email_verified_at);

        $this->postJson('/api/set-admin', ['admin_code' => 'adm-budi'])
            ->assertOk()
            ->assertJson(['success' => true, 'redirect' => '/admin/dashboard']);

        $user->refresh();
        $this->assertSame('admin', $user->role);
        $this->assertSame('ADM-BUDI', $user->admin_code);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_login_with_email_is_case_insensitive(): void
    {
        $user = User::factory()->create([
            'email' => 'Mixed@Example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $this->postJson('/api/login', [
            'login_id' => 'mixed@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJson(['redirect' => '/admin/dashboard']);

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_username_that_differs_only_by_case(): void
    {
        User::factory()->create(['username' => 'Adminapp']);

        $this->postJson('/api/register', $this->registerPayload([
            'username' => 'adminapp',
            'email' => 'new@example.com',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('username');
    }

    public function test_registration_rejects_email_that_differs_only_by_case(): void
    {
        User::factory()->create(['email' => 'Admin@Example.com']);

        $this->postJson('/api/register', $this->registerPayload([
            'username' => 'newuser',
            'email' => 'admin@example.com',
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_otp_is_locked_after_too_many_wrong_attempts(): void
    {
        $otp = $this->registerAndGetOtp();

        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $this->postJson('/api/verify-otp', ['otp' => '000000'])
                ->assertStatus(400);
        }

        $this->postJson('/api/verify-otp', ['otp' => '000000'])
            ->assertStatus(429);

        $this->postJson('/api/verify-otp', ['otp' => $otp])
            ->assertStatus(429);

        $this->assertNull(Cache::get('otp_budi@example.com'));
    }

    public function test_forgot_password_does_not_reveal_unregistered_email(): void
    {
        $this->postJson('/api/forgot-password/send-otp', ['email' => 'nobody@example.com'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertNull(Cache::get('reset_otp_nobody@example.com'));
    }

    public function test_password_reset_with_otp_updates_password(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        $this->postJson('/api/forgot-password/send-otp', ['email' => 'reset@example.com'])
            ->assertOk();

        $otp = (string) Cache::get('reset_otp_reset@example.com');

        $this->assertNotSame('', $otp);

        $this->postJson('/api/forgot-password/reset', [
            'email' => 'reset@example.com',
            'otp' => $otp,
            'password' => 'new-password-123',
            'confirm_password' => 'new-password-123',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_sales_can_only_connect_to_an_existing_admin_code_once(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'admin_code' => 'ADMIN1',
        ]);

        $sales = User::factory()->create([
            'role' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($sales)
            ->postJson('/api/set-sales', ['admin_code' => 'admin1'])
            ->assertOk()
            ->assertJson(['redirect' => '/sales/home']);

        $sales->refresh();
        $this->assertSame('sales', $sales->role);
        $this->assertSame($admin->id, $sales->parent_admin_id);
        $this->assertNull($sales->admin_code);

        $this->actingAs($sales)
            ->postJson('/api/set-admin', ['admin_code' => 'OTHER'])
            ->assertStatus(409);

        $nonAdmin = User::factory()->create([
            'role' => null,
            'admin_code' => 'FAKE',
        ]);

        $otherSales = User::factory()->create([
            'role' => null,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($otherSales)
            ->postJson('/api/set-sales', ['admin_code' => $nonAdmin->admin_code])
            ->assertStatus(404);
    }

    public function test_verified_users_are_redirected_away_from_verification_and_role_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/verify-email')
            ->assertRedirect('/');

        $this->actingAs($admin)
            ->get('/select-role')
            ->assertRedirect('/admin/dashboard');
    }
}
