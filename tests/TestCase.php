<?php
namespace Tests;
use App\Models\User;
use Illuminate\Support\Str;
abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    protected function loginAccount(array $attributes = []): User
    {
        return User::create(array_merge([
            'username' => 'reviewer_'.Str::lower(Str::random(10)),
            'full_name' => 'Test Reviewer', 'email' => Str::lower(Str::random(12)).'@example.com',
            'password' => 'ValidPassword123!',
        ], $attributes));
    }
}
