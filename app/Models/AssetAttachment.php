<?php

namespace App\Models;

use Database\Factories\AssetAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['asset_id', 'file_path', 'file_name', 'mime_type', 'file_size', 'uuid'])]
class AssetAttachment extends Model implements Auditable
{
    /** @use HasFactory<AssetAttachmentFactory> */
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
            'file_size' => 'integer',
        ];
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
