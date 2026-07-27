<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetTransaction;
use App\Models\Category;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_users' => User::count(),
            'total_transactions' => AssetTransaction::count(),
        ];

        // Get 5 latest transactions with asset and user relations
        $latestTransactions = AssetTransaction::with(['asset', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'latestTransactions' => $latestTransactions,
        ]);
    }
}
