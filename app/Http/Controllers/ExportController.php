<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Exports\CategoriesExport;
use App\Exports\UsersExport;
use App\Jobs\ProcessAssetExport;
use App\Jobs\ProcessCategoryExport;
use App\Jobs\ProcessUserExport;
use App\Models\DataJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ExportController extends Controller
{
    public function storeAssets(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fields'   => ['nullable', 'array'],
            'fields.*' => ['in:' . implode(',', array_keys(AssetsExport::AVAILABLE_FIELDS))],
        ]);

        $exportJob = DataJob::create([
            'user_id' => auth()->id(),
            'type'    => 'assets',
            'status'  => 'queued',
        ]);

        ProcessAssetExport::dispatch($exportJob->id, $validated['fields'] ?? []);

        return redirect()->back()->with('success', 'File sedang diproses di background.');
    }

    public function storeUsers(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fields'   => ['nullable', 'array'],
            'fields.*' => ['in:' . implode(',', array_keys(UsersExport::AVAILABLE_FIELDS))],
        ]);

        $exportJob = DataJob::create([
            'user_id' => auth()->id(),
            'type'    => 'users',
            'status'  => 'queued',
        ]);

        ProcessUserExport::dispatch($exportJob->id, $validated['fields'] ?? []);

        return redirect()->back()->with('success', 'File sedang diproses di background.');
    }

    public function storeCategories(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fields'   => ['nullable', 'array'],
            'fields.*' => ['in:' . implode(',', array_keys(CategoriesExport::AVAILABLE_FIELDS))],
        ]);

        $exportJob = DataJob::create([
            'user_id' => auth()->id(),
            'type'    => 'categories',
            'status'  => 'queued',
        ]);

        ProcessCategoryExport::dispatch($exportJob->id, $validated['fields'] ?? []);

        return redirect()->back()->with('success', 'File sedang diproses di background.');
    }
}
