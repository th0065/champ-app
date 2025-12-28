<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
{
    $stats = [
        'revenue' => \App\Models\Order::where('status', 'delivered')->sum('total_amount'),
        'pending' => \App\Models\Order::where('status', 'pending')->count(),
        'products' => \App\Models\Product::count(),
    ];

    $pendingOrders = \App\Models\Order::where('status', 'pending')
                        ->with('user')
                        ->latest()
                        ->get();

    return view('admin.dashboard', compact('pendingOrders', 'stats'));
}
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'Commande mise à jour !');
    }
}