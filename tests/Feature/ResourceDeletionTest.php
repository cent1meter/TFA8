<?php
namespace Tests\Feature;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class ResourceDeletionTest extends TestCase
{
    use RefreshDatabase;
    public function test_customer_can_be_deleted_by_an_authenticated_user(): void
    {
        $this->actingAs($this->loginAccount());
        $customer = Customer::create(['full_name' => 'Delete Me', 'email' => 'delete@example.com']);
        $this->delete("/customers/{$customer->id}")->assertRedirect(route('customers.index'));
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
    public function test_deleting_another_user_removes_their_avatar(): void
    {
        Storage::fake('public');
        $actor = $this->loginAccount();
        $target = $this->loginAccount(['avatar' => 'avatars/22222222-2222-4222-8222-222222222222.jpg']);
        Storage::disk('public')->put($target->avatar, 'old image');
        $this->actingAs($actor)->delete("/users/{$target->id}")->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        Storage::disk('public')->assertMissing($target->avatar);
    }
    public function test_deleting_the_current_login_is_rejected(): void
    {
        $actor = $this->loginAccount();
        $this->actingAs($actor)->delete("/users/{$actor->id}")->assertRedirect(route('users.index'))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $actor->id]);
    }
    public function test_get_requests_do_not_delete_records(): void
    {
        $this->actingAs($this->loginAccount());
        $customer = Customer::create(['full_name' => 'Keep Me', 'email' => 'keep@example.com']);
        $this->get("/customers/{$customer->id}")->assertOk()->assertSee('Keep Me');
        $this->get("/customers/{$customer->id}/destroy")->assertNotFound();
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}
