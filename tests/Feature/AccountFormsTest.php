<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AccountFormsTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->loginAccount());
    }
    public function test_create_and_edit_forms_are_reachable_and_prefilled(): void
    {
        $customer = Customer::create(['full_name' => 'Test Customer', 'email' => 'test@example.com']);
        $user = User::create(['username' => 'teststaff', 'full_name' => 'Test Staff']);
        $this->get('/customers/create')->assertOk()->assertSee('Create Customer');
        $this->get('/users/create')->assertOk()->assertSee('Create User');
        $this->get("/customers/{$customer->id}/edit")->assertOk()->assertSee('value="Test Customer"', false)->assertSee('value="test@example.com"', false);
        $this->get("/users/{$user->id}/edit")->assertOk()->assertSee('value="teststaff"', false)->assertSee('multipart/form-data');
        $this->get('/customers/999999/edit')->assertNotFound();
        $this->get('/users/999999/edit')->assertNotFound();
    }
    public function test_customer_creation_and_update_persist_validated_fields(): void
    {
        $this->post('/customers', ['full_name' => 'New Customer', 'email' => 'new@example.com', 'phone' => '09171234567', 'id' => 9000])
            ->assertRedirect(route('customers.index'))->assertSessionHas('success');
        $customer = Customer::firstOrFail();
        $this->assertNotSame(9000, $customer->id);
        $this->put("/customers/{$customer->id}", ['full_name' => 'Edited Customer', 'email' => 'edited@example.com', 'phone' => ''])
            ->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Edited Customer', 'email' => 'edited@example.com', 'phone' => null]);
    }
    public function test_customer_validation_redirects_with_errors_and_old_input(): void
    {
        $this->from('/customers/create')->post('/customers', ['full_name' => 'Remember this name', 'email' => 'invalid'])
            ->assertRedirect('/customers/create')->assertSessionHasErrors('email')->assertSessionHasInput('full_name', 'Remember this name');
        $this->get('/customers/create')->assertSee('value="Remember this name"', false)->assertSee('Enter a valid email address.');
        $this->post('/customers', [])->assertSessionHasErrors(['full_name', 'email']);
        $this->post('/customers', ['full_name' => str_repeat('x', 101), 'email' => 'ok@example.com', 'phone' => str_repeat('1', 21)])
            ->assertSessionHasErrors(['full_name', 'phone']);
        $this->assertDatabaseCount('customers', 0);
    }
    public function test_invalid_customer_update_does_not_change_the_record(): void
    {
        $customer = Customer::create(['full_name' => 'Original', 'email' => 'original@example.com']);
        $this->from("/customers/{$customer->id}/edit")->put("/customers/{$customer->id}", ['full_name' => 'Changed', 'email' => 'bad'])
            ->assertSessionHasErrors('email')->assertSessionHasInput('full_name', 'Changed');
        $this->assertSame('Original', $customer->fresh()->full_name);
    }
    public function test_user_creation_requires_fields_and_rejects_duplicate_username(): void
    {
        $this->post('/users', [])->assertSessionHasErrors(['username', 'full_name']);
        $this->post('/users', ['username' => 'newstaff', 'full_name' => 'New Staff'])
            ->assertRedirect(route('users.index'))->assertSessionHas('success');
        $this->from('/users/create')->post('/users', ['username' => 'newstaff', 'full_name' => 'Another Staff'])
            ->assertSessionHasErrors('username')->assertSessionHasInput('full_name', 'Another Staff');
        $this->assertDatabaseCount('users', 2);
        $this->post('/users', ['username' => str_repeat('a', 51), 'full_name' => str_repeat('b', 101)])
            ->assertSessionHasErrors(['username', 'full_name']);
    }
    public function test_user_can_keep_username_or_change_to_an_unused_one(): void
    {
        $user = User::create(['username' => 'existing', 'full_name' => 'Before']);
        $this->put("/users/{$user->id}", ['username' => 'existing', 'full_name' => 'After'])->assertRedirect(route('users.index'));
        $this->assertSame('After', $user->fresh()->full_name);
        $this->put("/users/{$user->id}", ['username' => 'renamed', 'full_name' => 'After'])->assertRedirect(route('users.index'));
        $this->assertSame('renamed', $user->fresh()->username);
    }
    public function test_submitted_id_cannot_bypass_update_uniqueness(): void
    {
        $first = User::create(['username' => 'first', 'full_name' => 'First']);
        $second = User::create(['username' => 'second', 'full_name' => 'Second']);
        $this->put("/users/{$first->id}", ['username' => 'second', 'full_name' => 'Changed', 'id' => $second->id])
            ->assertSessionHasErrors('username');
        $this->assertSame('first', $first->fresh()->username);
    }
}
