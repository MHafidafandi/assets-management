<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

#[Fillable(['asset_id', 'file_path', 'file_name', 'mime_type', 'file_size'])]
class AssetAttachment extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\AssetAttachmentFactory> */
    use HasFactory, \OwenIt\Auditing\Auditable;

    function casts(): array
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
