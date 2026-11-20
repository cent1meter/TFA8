<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public function test_breeze_package_is_installed_for_development(): void
    {
        $this->assertTrue(class_exists(\Laravel\Breeze\BreezeServiceProvider::class));
    }
    public function test_login_and_registration_forms_are_available_to_guests(): void
    {
        $this->get('/login')->assertOk()->assertSee('name="password"', false);
        $this->get('/register')->assertOk()->assertSee('name="username"', false);
    }
    public function test_registration_hashes_password_and_logs_the_user_in(): void
    {
        Event::fake([Registered::class]);
        $this->post('/register', [
            'username' => 'rainier.demo', 'full_name' => 'Rainier Ian Sayson',
            'email' => 'RAINIER@example.com', 'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
        ])->assertRedirect('/dashboard');
        $user = User::firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('rainier@example.com', $user->email);
        $this->assertTrue(Hash::check('StrongPassword123!', $user->password));
        $this->assertNotSame('StrongPassword123!', $user->password);
        $this->assertArrayNotHasKey('password', $user->toArray());
        Event::assertDispatched(Registered::class);
    }
    public function test_registration_rejects_duplicate_identity_and_unconfirmed_password(): void
    {
        $user = $this->loginAccount();
        $this->post('/register', ['username' => $user->username, 'full_name' => 'Another', 'email' => $user->email, 'password' => 'short', 'password_confirmation' => 'mismatch'])
            ->assertSessionHasErrors(['username', 'email', 'password']);
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }
    public function test_valid_login_rotates_session_and_logout_removes_access(): void
    {
        $user = $this->loginAccount(['email' => 'login@example.com']);
        $this->get('/login');
        $oldId = session()->getId();
        $this->post('/login', ['email' => 'LOGIN@example.com', 'password' => 'ValidPassword123!'])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldId, session()->getId());
        $loggedInId = session()->getId();
        $token = session()->token();
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->assertNotSame($loggedInId, session()->getId());
        $this->assertNotSame($token, session()->token());
        $this->get('/users')->assertRedirect(route('login'));
    }
    public function test_wrong_password_is_rejected_and_password_is_not_flashed(): void
    {
        $user = $this->loginAccount();
        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertArrayNotHasKey('password', session()->getOldInput());
    }
    public function test_login_is_throttled_after_five_failures(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->from('/login')->post('/login', ['email' => 'unknown@example.com', 'password' => 'wrong-password'])->assertSessionHasErrors('email');
        }
        $this->get('/login')->assertSee('Too many login attempts.');
        $this->assertGuest();
    }
    public function test_signed_in_users_cannot_reopen_guest_auth_forms(): void
    {
        $this->actingAs($this->loginAccount());
        $this->get('/login')->assertRedirect('/dashboard');
        $this->get('/register')->assertRedirect('/dashboard');
    }
    public function test_password_update_requires_current_password(): void
    {
        $user = $this->loginAccount();
        $this->actingAs($user)->from('/account/security')->put('/password', ['current_password' => 'wrong', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])
            ->assertSessionHasErrorsIn('updatePassword', ['current_password']);
        $this->assertTrue(Hash::check('ValidPassword123!', $user->fresh()->password));
        $this->put('/password', ['current_password' => 'ValidPassword123!', 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])
            ->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }
    public function test_remember_me_sets_a_token_and_get_cannot_log_out(): void
    {
        $user = $this->loginAccount();
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'ValidPassword123!', 'remember' => '1']);
        $response->assertRedirect('/dashboard')->assertCookie(\Illuminate\Support\Facades\Auth::guard('web')->getRecallerName());
        $this->assertNotEmpty($user->fresh()->remember_token);
        $this->get('/logout')->assertStatus(405);
        $this->assertAuthenticatedAs($user);
    }
}
