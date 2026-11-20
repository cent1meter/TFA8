<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Customer extends Model
{
    // TFA2 has created_at only; do not write a nonexistent updated_at column.
    public const UPDATED_AT = null;
    protected $fillable = ['full_name', 'email', 'phone'];
    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
