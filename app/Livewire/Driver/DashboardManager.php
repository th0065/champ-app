<?php

namespace App\Livewire\Driver;

use Livewire\Component;
use App\Models\Delivery;
use Illuminate\Support\Facades\Auth;

class DashboardManager extends Component
{
    public $view = 'list';
    public $selectedDeliveryId = null;
    public $comment = '';
    public $isAvailable;

    /**
     * Initialisation de l'état de disponibilité au chargement
     */
    public function mount()
    {
        $this->isAvailable = Auth::user()->is_available;
    }

    /**
     * Basculer la disponibilité du livreur
     */
    public function toggleAvailability()
    {
        $this->isAvailable = !$this->isAvailable;
        $user = Auth::user();
        $user->is_available = $this->isAvailable;
        $user->save();

        $status = $this->isAvailable ? 'disponible' : 'indisponible';
        session()->flash('success', "Vous êtes maintenant $status.");
    }

    public function showDetails($id)
    {
        $this->selectedDeliveryId = $id;
        $this->view = 'details';
    }

    public function goBack()
    {
        $this->view = 'list';
        $this->reset(['selectedDeliveryId', 'comment']);
    }

    public function confirmDelivery()
    {
        $delivery = Delivery::findOrFail($this->selectedDeliveryId);
        
        $delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        // Mise à jour de la commande parente
        $delivery->order->update(['status' => 'delivered']);

        session()->flash('success', 'Livraison confirmée pour la commande #' . $delivery->order_id);
        $this->goBack();
    }

    public function render()
    {
        // On récupère les livraisons (on les affiche même si indisponible car elles sont déjà assignées)
        $deliveries = Delivery::where('driver_id', Auth::id())
            ->whereIn('status', ['assigned', 'on_delivery'])
            ->with(['order.user', 'order.items.product'])
            ->get();

        $current = $this->selectedDeliveryId 
            ? Delivery::with(['order.user', 'order.items.product'])->find($this->selectedDeliveryId) 
            : null;

        return view('livewire.driver.dashboard-manager', compact('deliveries', 'current'));
    }
}