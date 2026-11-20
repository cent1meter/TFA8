<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CsrfProtectionTest extends TestCase
{
    use RefreshDatabase;
    public function test_browser_write_without_csrf_token_is_rejected(): void
    {
        $this->actingAs($this->loginAccount());
        // Laravel bypasses CSRF in its normal test environment; enable the real check for this test.
        $this->app->instance('env', 'local');
        $this->post('/customers', ['full_name' => 'Blocked', 'email' => 'blocked@example.com'])->assertStatus(419);
        $this->assertDatabaseCount('customers', 0);
    }
}
