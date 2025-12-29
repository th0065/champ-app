<?php


namespace App\Livewire\Delivery;

use App\Models\User;
use App\Models\Order;
use App\Models\Delivery;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    // Pour la modale Driver
    public $showingDriverModal = false;
    public $name, $email, $phone;

    // Pour l'attribution des commandes
    public $selectedDrivers = []; 

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required|unique:users,phone',
    ];

    // --- GESTION DES DRIVERS ---

    public function openModal()
    {
        $this->reset(['name', 'email', 'phone']);
        $this->resetErrorBag();
        $this->showingDriverModal = true;
    }

    public function saveDriver()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make('password123'),
            'role' => 'driver',
        ]);

        $this->showingDriverModal = false;
    }

    public function deleteDriver($id)
    {
        User::where('id', $id)->where('role', 'driver')->delete();
    }

    // --- LOGIQUE D'ATTRIBUTION (DELIVERY) ---

    public function assignOrder($orderId)
    {
        $driverId = $this->selectedDrivers[$orderId] ?? null;

        if (!$driverId) return;

        // Créer l'enregistrement dans la table deliveries
        Delivery::create([
            'order_id' => $orderId,
            'driver_id' => $driverId,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // Mettre à jour le statut de la commande originale
        Order::where('id', $orderId)->update(['status' => 'processing']);

        // Nettoyer la sélection
        unset($this->selectedDrivers[$orderId]);
    }

   public function render()
{
    return view('livewire.delivery.index', [
        'drivers' => User::where('role', 'driver')->get(),
        
        // Détails complets pour "Orders awaiting assignment"
        'pendingOrders' => Order::where('status', 'pending')
                            ->whereDoesntHave('delivery')
                            ->with('user') // Pour le nom du client
                            ->latest()
                            ->get(),

        // Pour le "Suivi des livraisons en cours"
        'ongoingDeliveries' => Delivery::whereIn('status', ['assigned', 'on_delivery'])
                                ->with(['order', 'driver'])
                                ->get(),
    ]);
}
}