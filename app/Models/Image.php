<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Image extends Model
{
    protected $fillable = ['response_id', 'file_name', 'path', 'mime_type', 'size', 'status'];


    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'response_id' => 'integer',
            'created_at' => 'datetime'
        ];
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }
}
