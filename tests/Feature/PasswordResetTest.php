<?php
namespace Tests\Feature;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;
    public function test_reset_link_and_token_update_the_password_once(): void
    {
        Notification::fake();
        $user = $this->loginAccount();
        $this->get('/forgot-password')->assertOk();
        $this->from('/forgot-password')->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $this->get('/reset-password/'.$notification->token.'?email='.urlencode($user->email))->assertOk();
            $data = ['token' => $notification->token, 'email' => $user->email, 'password' => 'ResetPassword123!', 'password_confirmation' => 'ResetPassword123!'];
            $this->post('/reset-password', $data)->assertRedirect(route('login'));
            $this->assertTrue(Hash::check('ResetPassword123!', $user->fresh()->password));
            $this->post('/reset-password', $data)->assertSessionHasErrors('email');
            return true;
        });
    }
}
