<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
class DatabaseTest extends TestCase
{
    use RefreshDatabase;
    public function test_seed_is_complete_and_can_run_twice(): void
    {
        $this->seed(); $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseHas('users', ['username' => 'admin01', 'full_name' => 'Rainier Ian Sayson']);
        $this->assertNotNull(Customer::first()->created_at);
        $this->assertNotNull(User::first()->created_at);
    }
    public function test_schema_preserves_tfa7_and_adds_authentication(): void
    {
        $this->assertEqualsCanonicalizing(['id', 'full_name', 'email', 'phone', 'created_at'], Schema::getColumnListing('customers'));
        $this->assertEqualsCanonicalizing(['id', 'username', 'full_name', 'created_at', 'avatar', 'email', 'email_verified_at', 'password', 'remember_token'], Schema::getColumnListing('users'));
    }
    public function test_username_is_unique(): void
    {
        User::create(['username' => 'unique01', 'full_name' => 'First user']);
        $this->expectException(QueryException::class);
        User::create(['username' => 'unique01', 'full_name' => 'Second user']);
    }
    public function test_model_updates_work_without_updated_at_column(): void
    {
        $user = User::create(['username' => 'editable', 'full_name' => 'Before']);
        $user->update(['full_name' => 'After']);
        $this->assertSame('After', $user->fresh()->full_name);
    }
}
