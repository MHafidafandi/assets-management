<?php

namespace App\Models;

use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['name', 'category_id', 'purchase_date', 'specification', 'is_active', 'uuid'])]
class Asset extends Model implements Auditable
{
    /** @use HasFactory<AssetFactory> */
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

    public function attachment()
    {
        return $this->hasOne(AssetAttachment::class)->latestOfMany();
    }
}
