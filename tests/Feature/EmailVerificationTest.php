<?php
namespace Tests\Feature;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;
    public function test_verification_requires_a_valid_signature_and_marks_the_account(): void
    {
        Event::fake([Verified::class]);
        $user = $this->loginAccount();
        $this->actingAs($user)->get('/verify-email/'.$user->id.'/'.sha1($user->email))->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->get($url)->assertRedirect('/dashboard?verified=1');
        $this->assertNotNull($user->fresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }
    public function test_verification_notification_can_be_requested(): void
    {
        Notification::fake();
        $user = $this->loginAccount();
        $this->actingAs($user)->get('/verify-email')->assertOk();
        $this->post('/email/verification-notification')->assertSessionHas('status', 'verification-link-sent');
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
