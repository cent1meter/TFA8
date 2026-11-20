<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    // Preserve the original TFA6/TFA7 created_at-only schema.
    public const UPDATED_AT = null;
    protected $fillable = ['username', 'full_name', 'avatar', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['created_at' => 'datetime', 'email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }
        return asset('images/avatar-placeholder.svg');
    }
}
