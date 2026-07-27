<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\DataJob;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetTransactionController extends Controller
{
    public function index()
    {
        $transactions = AssetTransaction::query()
            ->with(['asset:id,name', 'user:id,name,email'])
            ->when(request('search'), function ($query, $search) {
                $query->whereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when(request()->filled('is_returned'), function ($query) {
                $isReturned = request('is_returned');
                $isReturnedBool = filter_var($isReturned, FILTER_VALIDATE_BOOLEAN);

                return $query->where('is_returned', $isReturnedBool);
            })
            ->when(
                request('sort'),
                function ($q, $sort) {
                    $column = ltrim($sort, '-');
                    if (in_array($column, ['asset_id', 'user_id', 'borrowed_at', 'returned_at', 'is_returned', 'created_at'])) {
                        $q->orderBy($column, str_starts_with($sort, '-') ? 'desc' : 'asc');
                    } else {
                        $q->orderBy('created_at', 'desc');
                    }
                },
                fn ($q) => $q->orderBy('created_at', 'desc')
            )
            ->paginate(10)
            ->withQueryString();

        $assets = Asset::select('id', 'name')->get();
        $users = User::select('id', 'name', 'email')->get();
        $exportJob = DataJob::query()
            ->where('type', 'transactions')
            ->where('direction', 'export')
            ->latest()
            ->first();
        $importJob = DataJob::query()
            ->where('type', 'transactions')
            ->where('direction', 'import')
            ->latest()
            ->first();

        return Inertia::render('transactions/Index', [
            'transactions' => $transactions,
            'assets' => $assets,
            'users' => $users,
            'exportJob' => $exportJob,
            'importJob' => $importJob,
            'filters' => request()->only(['search', 'is_returned', 'sort']),
            'audits' => Inertia::optional(fn () => $this->getAudits()),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'borrowed_at' => 'required|date',
            'returned_at' => 'nullable|date|after_or_equal:borrowed_at',
            'is_returned' => 'required|boolean',
            'notes' => 'nullable|array',
        ]);

        AssetTransaction::create($validatedData);

        return redirect()->back()->with('success', 'Transaction created successfully.');
    }

    public function show(string $id) {}

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'borrowed_at' => 'required|date',
            'returned_at' => 'nullable|date|after_or_equal:borrowed_at',
            'is_returned' => 'required|boolean',
            'notes' => 'nullable|array',
        ]);

        $transaction = AssetTransaction::findOrFail($id);
        $transaction->update($validatedData);

        return redirect()->back()->with('success', 'Transaction updated successfully.');
    }

    public function destroy(string $id)
    {
        $transaction = AssetTransaction::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }
}
