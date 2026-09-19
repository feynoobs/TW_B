<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Response extends Model
{
    use HasFactory, searchable;

    protected $fillable = [
        'thread_id', 'body', 'name', 'mail', 'hash', 'wacchoi', 'status'
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'created_at' => 'datetime'
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
