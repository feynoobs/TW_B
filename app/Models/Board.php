<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'slug', 'name', 'status'];


    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'created_at' => 'datetime'
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }


    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }
}
