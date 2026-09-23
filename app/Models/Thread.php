<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Thread extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'board_id', 'title', 'status', 'modified_at'
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'modified_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class)->orderBy('id', 'asc');
    }
}
