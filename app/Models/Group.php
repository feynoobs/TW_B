<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = ['slug', 'name', 'status', 'sort'];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
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
