<?php

namespace App\Livewire\Buyer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class RecentOrders extends Component
{
    public function render()
    {
        $orders = Auth::user()->orders()
            ->with(['items.product']) // Pour afficher ce qu'il y a dedans si besoin
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.buyer.recent-orders', compact('orders'));
    }
}