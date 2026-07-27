<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['asset_id', 'user_id', 'borrowed_at', 'returned_at', 'is_returned', 'notes'])]
class AssetTransaction extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetTransactionFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    public function casts(): array
    {
        return [
            'notes' => 'array',
            'borrowed_at' => 'datetime',
            'returned_at' => 'datetime',
            'is_returned' => 'boolean',
        ];
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
