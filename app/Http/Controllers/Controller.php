<?php

namespace App\Http\Controllers;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

abstract class Controller
{
    protected function getAudits()
    {
        $type = request('auditable_type');
        if ($type) {
            if (! str_contains($type, '\\')) {
                $type = 'App\\Models\\'.ucfirst($type);
            }
            if (class_exists($type) && in_array(Auditable::class, class_implements($type))) {
                return Audit::where('auditable_type', $type)
                    ->with('user:id,name,email')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            }
        }

        return null;
    }
}
