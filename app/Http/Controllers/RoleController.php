<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()
            ->select('id', 'name', 'description', 'created_at')
            ->when(request('search'), function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(request('sort'), function ($q, $sort) {
                $column = ltrim($sort, '-');
                if (in_array($column, ['name', 'description', 'created_at'])) {
                    $q->orderBy($column, str_starts_with($sort, '-') ? 'desc' : 'asc');
                } else {
                    $q->orderBy('created_at', 'desc');
                }
            }, fn ($q) => $q->orderBy('created_at', 'desc'))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('roles/Index', [
            'roles' => $roles,
            'filters' => request()->only(['search', 'sort']),
            'audits' => Inertia::optional(fn () => $this->getAudits()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
        ]);

        Role::create($validatedData);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string|max:500',
        ]);

        $role->update($validatedData);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
