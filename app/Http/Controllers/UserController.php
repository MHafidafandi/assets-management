<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\DataJob;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->with('role:id,name')
            ->select('id', 'name', 'email', 'role_id', 'is_active')
            ->when(request('search'), function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(request('role_id'), fn ($q, $roleId) => $q->where('role_id', $roleId))
            ->when(request()->filled('is_active'), function ($q) {
                $isActive = request('is_active');
                $isActiveBool = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);

                return $q->where('is_active', $isActiveBool);
            })
            ->when(
                request('sort'),
                function ($q, $sort) {
                    $column = ltrim($sort, '-');
                    if (in_array($column, ['name', 'email', 'role_id', 'is_active', 'created_at'])) {
                        $q->orderBy($column, str_starts_with($sort, '-') ? 'desc' : 'asc');
                    } else {
                        $q->orderBy('created_at', 'desc');
                    }
                },
                fn ($q) => $q->orderBy('created_at', 'desc')
            )
            ->paginate(10)
            ->withQueryString();

        $roles = Role::select('id', 'name')->get();

        $exportJob = DataJob::where('user_id', auth()->id())
            ->where('type', 'users')
            ->where('direction', 'export')
            ->latest()
            ->first();
        $importJob = DataJob::where('user_id', auth()->id())
            ->where('type', 'users')
            ->where('direction', 'import')
            ->latest()
            ->first();

        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => request()->only(['search', 'role_id', 'is_active', 'sort']),
            'exportJob' => $exportJob,
            'importJob' => $importJob,
            'audits' => Inertia::optional(fn () => $this->getAudits()),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            ...$request->validated(),
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
