<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DataJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()
            ->select('id', 'name', 'is_active')
            ->withCount('assets')
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when(request()->filled('is_active'), function ($query) {
                $isActive = request('is_active');
                $isActiveBool = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);

                return $query->where('is_active', $isActiveBool);
            })
            ->when(
                request('sort'),
                function ($q, $sort) {
                    $column = ltrim($sort, '-');
                    if (in_array($column, ['name', 'is_active', 'created_at'])) {
                        $q->orderBy($column, str_starts_with($sort, '-') ? 'desc' : 'asc');
                    } else {
                        $q->orderBy('created_at', 'desc');
                    }
                },
                fn ($q) => $q->orderBy('created_at', 'desc')
            )
            ->paginate(10)
            ->withQueryString();

        $exportJob = DataJob::where('user_id', auth()->id())
            ->where('type', 'categories')
            ->where('direction', 'export')
            ->latest()
            ->first();
        $importJob = DataJob::where('user_id', auth()->id())
            ->where('type', 'categories')
            ->where('direction', 'import')
            ->latest()
            ->first();

        return Inertia::render('categories/Index', [
            'categories' => $categories,
            'filters' => request()->only(['search', 'is_active', 'sort']),
            'exportJob' => $exportJob,
            'importJob' => $importJob,
            'audits' => Inertia::optional(fn () => $this->getAudits()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        Category::create($validatedData);

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $category = Category::findOrFail($id);
        $category->update($validatedData);

        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category berhasil dihapus.');
    }
}
