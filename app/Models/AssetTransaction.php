<?php

namespace App\Models;

use Database\Factories\AssetTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['asset_id', 'user_id', 'borrowed_at', 'returned_at', 'is_returned', 'notes', 'uuid'])]
class AssetTransaction extends Model implements Auditable
{
    /** @use HasFactory<AssetTransactionFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

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
