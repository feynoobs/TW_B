<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'status', 'sort'];


    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'sort' => 'integer',
            'created_at' => 'datetime'
        ];
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }
}
