<?php
namespace Tests\Feature;
use App\Models\User;
use App\Services\AvatarStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use RuntimeException;
use Tests\TestCase;
class AvatarUploadTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->loginAccount());
        Storage::fake('public');
    }
    private function userWithAvatar(): User
    {
        $path = 'avatars/11111111-1111-4111-8111-111111111111.jpg';
        Storage::disk('public')->put($path, 'old image');
        return User::create(['username' => 'avatarstaff', 'full_name' => 'Avatar Staff', 'avatar' => $path]);
    }
    public function test_avatar_is_reencoded_as_a_square_jpeg_and_listed(): void
    {
        $user = User::create(['username' => 'photo', 'full_name' => 'Photo User']);
        $upload = UploadedFile::fake()->image('original.png', 900, 600);
        $this->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => 'photo', 'full_name' => 'Photo User', 'avatar' => $upload])
            ->assertRedirect(route('users.index'))->assertSessionHasNoErrors();
        $path = $user->fresh()->avatar;
        $this->assertMatchesRegularExpression('/\Aavatars\/[0-9a-f-]{36}\.jpg\z/', $path);
        Storage::disk('public')->assertExists($path);
        $info = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame(256, $info[0]);
        $this->assertSame(256, $info[1]);
        $this->assertSame('image/jpeg', $info['mime']);
        $this->assertCount(1, Storage::disk('public')->allFiles('avatars'));
        $this->get('/users')->assertOk()->assertSee(Storage::disk('public')->url($path), false);
    }
    public function test_upload_types_size_and_dimensions_are_checked_before_writing(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        $invalid = [
            UploadedFile::fake()->createWithContent('script.jpg', '<?php echo "test";'),
            UploadedFile::fake()->createWithContent('vector.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>'),
            UploadedFile::fake()->image('large.jpg')->size(2049),
            UploadedFile::fake()->image('wide.png', 4097, 10),
        ];
        foreach ($invalid as $file) {
            $this->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => $user->username, 'full_name' => 'Changed', 'avatar' => $file])
                ->assertSessionHasErrors('avatar');
            $this->assertSame($old, $user->fresh()->avatar);
            $this->assertSame('Avatar Staff', $user->fresh()->full_name);
            $this->assertSame([$old], Storage::disk('public')->allFiles('avatars'));
        }
    }
    public function test_upload_at_size_limit_is_accepted_and_old_file_removed(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        $this->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => $user->username, 'full_name' => 'New Name', 'avatar' => UploadedFile::fake()->image('new.jpg', 500, 300)->size(2048)])
            ->assertRedirect(route('users.index'))->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($user->fresh()->avatar);
        $this->assertSame('New Name', $user->fresh()->full_name);
    }
    public function test_update_without_upload_keeps_current_avatar_and_ignores_avatar_path_input(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        $this->put("/users/{$user->id}", ['username' => $user->username, 'full_name' => 'Renamed', 'avatar_path' => '../../other.jpg'])
            ->assertRedirect(route('users.index'));
        $this->assertSame($old, $user->fresh()->avatar);
        Storage::disk('public')->assertExists($old);
    }
    public function test_users_without_a_file_get_the_placeholder(): void
    {
        $first = User::create(['username' => 'none', 'full_name' => 'No Avatar']);
        $second = User::create(['username' => 'missing', 'full_name' => 'Missing File', 'avatar' => 'avatars/missing.jpg']);
        $url = asset('images/avatar-placeholder.svg');
        $this->assertSame($url, $first->avatar_url);
        $this->assertSame($url, $second->avatar_url);
        $this->get('/users')->assertOk()->assertSee($url, false);
    }
    public function test_invalid_user_fields_do_not_store_an_otherwise_valid_upload(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        $this->from("/users/{$user->id}/edit")->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => '', 'full_name' => 'Keep this input', 'avatar' => UploadedFile::fake()->image('valid.jpg')])
            ->assertSessionHasErrors('username')->assertSessionHasInput('full_name', 'Keep this input');
        $this->assertSame([$old], Storage::disk('public')->allFiles('avatars'));
        $this->get("/users/{$user->id}/edit")->assertSee('value="Keep this input"', false)->assertSee('Reselect the file');
    }
    public function test_database_failure_removes_new_upload_and_preserves_old_avatar(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        User::updating(function (): void { throw new RuntimeException('Simulated database failure'); });
        $this->withoutExceptionHandling();
        try {
            $this->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => $user->username, 'full_name' => 'Changed', 'avatar' => UploadedFile::fake()->image('replacement.png')]);
            $this->fail('The database failure should be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated database failure', $exception->getMessage());
        } finally {
            User::flushEventListeners();
        }
        $this->assertSame($old, $user->fresh()->avatar);
        $this->assertSame([$old], Storage::disk('public')->allFiles('avatars'));
    }
    public function test_storage_failure_does_not_modify_user_or_delete_old_file(): void
    {
        $user = $this->userWithAvatar();
        $old = $user->avatar;
        $service = Mockery::mock(AvatarStorage::class);
        $service->shouldReceive('store')->once()->andThrow(new RuntimeException('Simulated disk failure'));
        $service->shouldNotReceive('delete');
        $this->app->instance(AvatarStorage::class, $service);
        $this->withoutExceptionHandling();
        try {
            $this->post("/users/{$user->id}", ['_method' => 'PUT', 'username' => $user->username, 'full_name' => 'Changed', 'avatar' => UploadedFile::fake()->image('replacement.jpg')]);
            $this->fail('Storage failure should be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Simulated disk failure', $exception->getMessage());
        }
        $this->assertSame($old, $user->fresh()->avatar);
        $this->assertSame('Avatar Staff', $user->fresh()->full_name);
        Storage::disk('public')->assertExists($old);
    }
    public function test_corrupt_bytes_produce_an_avatar_validation_error(): void
    {
        try {
            (new AvatarStorage)->store(UploadedFile::fake()->createWithContent('broken.jpg', 'not an image'));
            $this->fail('Corrupt image should not decode.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('avatar', $exception->errors());
        }
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
