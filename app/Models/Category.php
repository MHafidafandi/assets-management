<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['name', 'is_active', 'uuid'])]
class Category extends Model implements Auditable
{
    /** @use HasFactory<CategoryFactory> */
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

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
