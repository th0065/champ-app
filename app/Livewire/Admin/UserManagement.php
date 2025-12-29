<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    
    // Propriétés pour le nouveau livreur
    public $name = '';
    public $email = '';
    public $showModal = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
    ];

    public function updatingSearch() { $this->resetPage(); }

    public function openModal() { $this->resetErrorBag(); $this->reset(['name', 'email']); $this->showModal = true; }

    public function saveDriver()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make('passer123'), // Mot de passe par défaut
            'role' => 'driver',
            'is_available' => false,
        ]);

        $this->showModal = false;
        session()->flash('message', 'Livreur ajouté avec succès ! (Mot de passe : passer123)');
    }

    public function toggleAvailability($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_available = !$user->is_available;
        $user->save();
    }

    public function render()
    {
        $drivers = User::where('role', 'driver')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'drivers' => $drivers
        ])->layout('components.layouts.app');
    }
}