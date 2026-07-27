<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAttachment;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::query()
            ->with(['category:id,name', 'attachment'])
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(request()->filled('is_active'), function ($query) {
                $isActive = request('is_active');
                $isActiveBool = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);

                return $query->where('is_active', $isActiveBool);
            })
            ->when(request('category_id'), function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when(
                request('sort'),
                function ($q, $sort) {
                    $column = ltrim($sort, '-');
                    if (in_array($column, ['name', 'purchase_date', 'is_active', 'category_id', 'created_at'])) {
                        $q->orderBy($column, str_starts_with($sort, '-') ? 'desc' : 'asc');
                    } else {
                        $q->orderBy('created_at', 'desc');
                    }
                },
                fn($q) => $q->orderBy('created_at', 'desc')
            )
            ->paginate(10)
            ->withQueryString();

        $categories = Category::select('id', 'name')->get();

        return Inertia::render('assets/Index', [
            'assets' => $assets,
            'categories' => $categories,
            'filters' => request()->only(['search', 'is_active', 'category_id', 'sort']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'specification' => 'nullable|array',
            'is_active' => 'required|boolean',
            'attachment' => 'nullable|file|mimes:pdf|min:100|max:500',
        ]);

        $asset = Asset::create($validatedData);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');

            \App\Models\AssetAttachment::create([
                'asset_id' => $asset->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => round($file->getSize() / 1024), // KB
            ]);
        }

        return redirect()->back()->with('success', 'Asset created successfully.');
    }

    public function show(string $id) {}

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'specification' => 'nullable|array',
            'is_active' => 'required|boolean',
            'attachment' => 'nullable|file|mimes:pdf|min:100|max:500',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update($validatedData);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');

            // Delete old attachment if exists
            $oldAttachment = AssetAttachment::where('asset_id', $asset->id)->first();
            if ($oldAttachment) {
                // Optionally delete physical file: Storage::disk('public')->delete($oldAttachment->file_path);
                $oldAttachment->delete();
            }

            \App\Models\AssetAttachment::create([
                'asset_id' => $asset->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType(),
                'file_size' => round($file->getSize() / 1024), // KB
            ]);
        }

        return redirect()->back()->with('success', 'Asset updated successfully.');
    }

    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->back()->with('success', 'Asset deleted successfully.');
    }
}
