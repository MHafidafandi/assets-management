<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAssetImport;
use App\Jobs\ProcessCategoryImport;
use App\Jobs\ProcessTransactionImport;
use App\Jobs\ProcessUserImport;
use App\Models\DataJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function assets(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        $path = $request->file('file')->store('imports');

        $dataJob = DataJob::create([
            'user_id'   => auth()->id(),
            'type'      => 'assets',
            'direction' => 'import',
            'status'    => 'queued',
        ]);

        ProcessAssetImport::dispatch($dataJob->id, $path);

        return back()->with('success', 'File sedang diproses di background.');
    }

    public function users(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        $path = $request->file('file')->store('imports');

        $dataJob = DataJob::create([
            'user_id'   => auth()->id(),
            'type'      => 'users',
            'direction' => 'import',
            'status'    => 'queued',
        ]);

        ProcessUserImport::dispatch($dataJob->id, $path);

        return back()->with('success', 'File sedang diproses di background.');
    }

    public function categories(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        $path = $request->file('file')->store('imports');

        $dataJob = DataJob::create([
            'user_id'   => auth()->id(),
            'type'      => 'categories',
            'direction' => 'import',
            'status'    => 'queued',
        ]);

        ProcessCategoryImport::dispatch($dataJob->id, $path);

        return back()->with('success', 'File sedang diproses di background.');
    }

    public function transactions(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv', 'max:5120'],
        ]);

        $path = $request->file('file')->store('imports');

        $dataJob = DataJob::create([
            'user_id'   => auth()->id(),
            'type'      => 'transactions',
            'direction' => 'import',
            'status'    => 'queued',
        ]);

        ProcessTransactionImport::dispatch($dataJob->id, $path);

        return back()->with('success', 'File sedang diproses di background.');
    }
}
