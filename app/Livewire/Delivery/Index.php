<?php

namespace App\Livewire\Delivery;

use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    public $showingDriverModal = false;
    public $name, $email, $phone;
    public $selectedDrivers = []; 

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|unique:users,phone',
    ];

    // --- GESTION DES DRIVERS ---

    public function toggleDriverAvailability($driverId)
    {
        $driver = User::findOrFail($driverId);
        $driver->is_available = !$driver->is_available;
        $driver->save();
    }

    // --- LOGIQUE D'ATTRIBUTION ---

    public function assignOrder($orderId)
    {
        $driverId = $this->selectedDrivers[$orderId] ?? null;

        if (!$driverId) {
            session()->flash('error', 'Veuillez choisir un livreur.');
            return;
        }

        Delivery::create([
            'order_id' => $orderId,
            'driver_id' => $driverId,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        Order::where('id', $orderId)->update(['status' => 'processing']);
        unset($this->selectedDrivers[$orderId]);
        
        session()->flash('message', 'Commande assignée avec succès !');
    }

    public function render()
    {
        // Tous les livreurs pour l'état de la flotte
        $allDrivers = User::where('role', 'driver')->get();

        // Uniquement les livreurs DISPONIBLES pour le menu déroulant d'attribution
        $availableDrivers = User::where('role', 'driver')
                                ->where('is_available', true)
                                ->get();

        return view('livewire.delivery.index', [
            'drivers' => $allDrivers,
            'availableDrivers' => $availableDrivers,
            'pendingOrders' => Order::where('status', 'pending')
                                ->whereDoesntHave('delivery')
                                ->with('user')
                                ->latest()
                                ->get(),
            'ongoingDeliveries' => Delivery::whereIn('status', ['assigned', 'on_delivery'])
                                ->with(['order', 'driver'])
                                ->get(),
        ])->layout('components.layouts.app');
    }
}