<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


#[Fillable(['user_id', 'type', 'direction', 'status', 'file_path', 'error_message', 'summary'])]
class DataJob extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }


    protected $casts = ['summary' => 'array'];
}
