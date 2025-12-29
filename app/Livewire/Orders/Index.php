<?php

namespace App\Livewire\Orders;

use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;
use Livewire\Component;

class Index extends Component
{
    public $filter = 'Tous';
    public $selectedOrder = null;
    public $driver_id;

    // CETTE MÉTHODE ÉTAIT MANQUANTE :
    public function setFilter($status)
    {
        $this->filter = $status;
        $this->selectedOrder = null; // On ferme le détail si on change de filtre
    }

    public function showOrder($id)
    {
        // On charge la commande avec ses relations
        $this->selectedOrder = Order::with(['user', 'items'])->find($id);
    }

    public function closeDetail()
    {
        $this->selectedOrder = null;
        $this->reset('driver_id');
    }

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['status' => $newStatus]);
            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->selectedOrder->refresh();
            }
        }
    }

    public function assignDriver()
    {
        $this->validate(['driver_id' => 'required|exists:users,id']);

        Delivery::updateOrCreate(
            ['order_id' => $this->selectedOrder->id],
            [
                'driver_id' => $this->driver_id,
                'status' => 'assigned',
                'assigned_at' => now()
            ]
        );

        $this->updateStatus($this->selectedOrder->id, 'processing');
        session()->flash('message', 'Livreur assigné !');
    }

    public function render()
    {
        $query = Order::with('user');

        if ($this->filter !== 'Tous') {
            $statusMapping = [
                'En attente' => 'pending',
                'En cours'   => 'processing',
                'Livré'      => 'delivered',
                'Annulé'     => 'canceled',
            ];
            $dbStatus = $statusMapping[$this->filter] ?? $this->filter;
            $query->where('status', $dbStatus);
        }

        return view('livewire.orders.index', [
            'orders' => $query->latest()->get(),
            'drivers' => User::where('role', 'driver')->get()
        ]);
    }
}