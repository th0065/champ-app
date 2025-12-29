<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    /**
     * Affiche le dashboard Admin avec statistiques et graphique.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            // 1. Statistiques du jour
            $stats = [
                'daily_sales' => Order::whereDate('created_at', Carbon::today())
                                    ->where('status', 'delivered')
                                    ->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'critical_stock' => Product::where('stock', '<', 10)->count(),
            ];

            // 2. Récupérer uniquement les UTILISATEURS avec le rôle 'driver'
            $drivers = User::where('role', 'driver')->get();

            // 3. Préparation des données pour la courbe (7 derniers jours)
            $labels = [];
            $salesData = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('d M'); // Ex: "28 Dec"
                
                // On récupère la somme des ventes pour ce jour précis
                $salesData[] = Order::whereDate('created_at', $date)
                                    ->where('status', 'delivered')
                                    ->sum('total_amount');
            }

            return view('dashboard', [
                'stats' => $stats,
                'drivers' => $drivers,
                'labels' => $labels,
                'salesData' => $salesData
            ]);
        }

        // Vue pour les clients (Acheteurs)
        $myOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        return view('dashboard', compact('myOrders'));
    }

    /**
     * Met à jour le statut d'une commande.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,delivered,cancelled']);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Statut de la commande mis à jour.');
    }
}