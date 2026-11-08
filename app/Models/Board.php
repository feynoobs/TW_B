<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 掲示板のモデル
 */
class Board extends Model
{
    protected $fillable = ['group_id', 'slug', 'name', 'name_nns', 'status', 'sort'];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'group_id' => 'integer',
            'status' => 'integer',
            'sort' => 'integer',
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
