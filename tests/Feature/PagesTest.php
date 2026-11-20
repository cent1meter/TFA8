<?php
namespace Tests\Feature;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class PagesTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->loginAccount());
    }
    public function test_all_pages_and_navigation_work(): void
    {
        $this->seed();
        foreach (['/', '/about', '/customers', '/users'] as $path) {
            $response = $this->get($path)->assertOk()->assertSee('SwiftPOS')->assertSee('css/styles.css');
            foreach (['home', 'about', 'customers.index', 'users.index'] as $name) {
                $response->assertSee('href="'.route($name).'"', false);
            }
        }
        $this->get('/missing-page')->assertNotFound();
    }
    public function test_every_seeded_record_is_rendered(): void
    {
        $this->seed();
        $response = $this->get('/customers')->assertOk()->assertViewIs('customers.index');
        foreach (Customer::all() as $row) {
            $response->assertSee($row->full_name)->assertSee($row->email)->assertSee($row->phone);
        }
        $response = $this->get('/users')->assertOk()->assertViewIs('users.index');
        foreach (User::all() as $row) {
            $response->assertSee($row->username)->assertSee($row->full_name);
        }
    }
    public function test_new_database_records_appear_without_changing_controller_code(): void
    {
        Customer::create(['full_name' => 'New customer', 'email' => 'new@example.com', 'phone' => null]);
        User::create(['username' => 'newstaff', 'full_name' => 'New staff']);
        $this->get('/customers')->assertOk()->assertSee('New customer')->assertSee('new@example.com');
        $this->get('/users')->assertOk()->assertSee('newstaff')->assertSee('New staff');
    }
    public function test_empty_tables_render_clear_messages(): void
    {
        $this->get('/customers')->assertOk()->assertSee('No customers found.');
        $this->get('/users')->assertOk()->assertSee('Test Reviewer');
        $this->get('/')->assertOk();
    }
    public function test_database_values_are_html_escaped(): void
    {
        Customer::create(['full_name' => '<script>alert(1)</script>', 'email' => 'escape@example.com', 'phone' => null]);
        $this->get('/customers')->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
