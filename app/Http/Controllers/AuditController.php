<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'auditable_type' => 'required|string',
            'auditable_id' => 'required|integer',
        ]);

        $type = $request->input('auditable_type');

        // Resolve model class. Supports 'Asset' or fully qualified namespace
        if (! str_contains($type, '\\')) {
            $type = 'App\\Models\\'.ucfirst($type);
        }

        if (! class_exists($type) || ! in_array(Auditable::class, class_implements($type))) {
            return response()->json(['error' => 'Invalid auditable type'], 400);
        }

        $audits = Audit::where('auditable_type', $type)
            ->where('auditable_id', $request->input('auditable_id'))
            ->with('user:id,name,email')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($audits);
    }
}
