<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['name', 'category_id', 'purchase_date', 'spesification', 'is_active'])]
class Asset extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    function casts(): array
    {
        return [
            'specification' => 'array',
            'purchase_date' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(AssetTransaction::class);
    }

    public function Attachments()
    {
        return $this->hasMany(AssetAttachment::class);
    }
}
