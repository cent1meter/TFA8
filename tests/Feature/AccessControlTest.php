<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;
class AccessControlTest extends TestCase
{
    use RefreshDatabase;
    public function test_every_resource_route_uses_authentication_and_conventional_actions(): void
    {
        foreach (['customers', 'users'] as $resource) {
            foreach (['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'] as $method) {
                $route = Route::getRoutes()->getByName("$resource.$method");
                $this->assertNotNull($route);
                $this->assertContains('auth', $route->gatherMiddleware());
                $this->assertStringEndsWith('@'.$method, $route->getActionName());
            }
        }
    }
    public function test_guests_are_redirected_on_every_resource_action_without_writes(): void
    {
        $customer = Customer::create(['full_name' => 'Original Customer', 'email' => 'original@example.com']);
        $user = $this->loginAccount(['full_name' => 'Original User']);
        foreach (['customers' => $customer->id, 'users' => $user->id] as $resource => $id) {
            foreach ([['GET', "/$resource"], ['GET', "/$resource/create"], ['GET', "/$resource/$id"], ['GET', "/$resource/$id/edit"], ['POST', "/$resource"], ['PUT', "/$resource/$id"], ['PATCH', "/$resource/$id"], ['DELETE', "/$resource/$id"]] as [$verb, $url]) {
                $this->call($verb, $url, ['full_name' => 'Unauthorized', 'email' => 'bad@example.com', 'username' => 'unauthorized'])
                    ->assertRedirect(route('login'));
            }
        }
        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('Original Customer', $customer->fresh()->full_name);
        $this->assertSame('Original User', $user->fresh()->full_name);
        $this->assertGuest();
    }
    public function test_logged_in_users_can_visit_all_resource_pages(): void
    {
        $actor = $this->loginAccount();
        $customer = Customer::create(['full_name' => 'Customer Details', 'email' => 'details@example.com']);
        $this->actingAs($actor);
        foreach (["/customers", '/customers/create', "/customers/{$customer->id}", "/customers/{$customer->id}/edit", '/users', '/users/create', "/users/{$actor->id}", "/users/{$actor->id}/edit"] as $path) {
            $this->get($path)->assertOk();
        }
    }
    public function test_intended_destination_is_reached_after_login(): void
    {
        $user = $this->loginAccount(['email' => 'intended@example.com']);
        $this->get('/customers/create')->assertRedirect(route('login'));
        $this->post('/login', ['email' => $user->email, 'password' => 'ValidPassword123!'])->assertRedirect(url('/customers/create'));
        $this->get('/customers/create')->assertOk();
    }
    public function test_home_and_about_remain_public(): void
    {
        $this->get('/')->assertOk()->assertSee('Log in');
        $this->get('/about')->assertOk();
    }
}
