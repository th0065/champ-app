  @if(auth()->user()->role === 'admin')
<div class="p-6">
    {{-- En-tête avec bouton Ajouter --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Product & Stock Management</h1>
        {{-- Ouvre la modale en mode "Ajout" --}}
        <button wire:click="openModal" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg transition shadow-sm">
            + Add Product
        </button>
    </div>

    {{-- Barre de recherche --}}
    <div class="mb-6 text-slate-800">
        <input 
            wire:model.live="search"
            type="text" 
            placeholder="Rechercher un fruit (orange, mangue...)" 
            class="w-full p-3 rounded-lg border border-zinc-200 focus:ring-2 focus:ring-emerald-500 outline-none"
        >
    </div>

    {{-- Tableau des Produits --}}
    <div class="bg-white border border-zinc-200 rounded-xl overflow-hidden shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-50 border-b border-zinc-200">
                <tr>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Image</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Product Name</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Price</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Unit</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Weight</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Stock</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600">Alert</th>
                    <th class="px-6 py-3 text-sm font-bold text-zinc-600 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200">
                @forelse($products as $product)
                    <tr class="hover:bg-zinc-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-12 h-12 border-2 border-dashed border-zinc-300 rounded flex items-center justify-center text-zinc-400 overflow-hidden">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xs">X</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-zinc-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-900">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                        <td class="px-6 py-4 text-sm text-zinc-500">/ {{ $product->unit }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $product->weight }} kg</td>
                        <td class="px-6 py-4 text-sm font-bold text-zinc-900">{{ $product->stock }}</td>
                        <td class="px-6 py-4">
                            @php
                                $alert = match(true) {
                                    $product->stock <= 20 => ['label' => 'Critical Stock', 'class' => 'bg-red-100 text-red-700'],
                                    $product->stock <= 100 => ['label' => 'Low Stock', 'class' => 'bg-yellow-100 text-yellow-700'],
                                    default => ['label' => 'In Stock', 'class' => 'bg-emerald-100 text-emerald-700'],
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $alert['class'] }}">
                                {{ $alert['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                            {{-- BOUTON EDIT : Appelle la méthode editProduct avec l'ID --}}
                            <button wire:click="editProduct({{ $product->id }})" class="px-3 py-1 text-xs font-bold text-white bg-blue-500 rounded hover:bg-blue-600 transition shadow-sm">
                                Edit
                            </button>
                            {{-- BOUTON DELETE : Appelle deleteProduct avec confirmation --}}
                            <button 
                                wire:click="deleteProduct({{ $product->id }})"
                                wire:confirm="Êtes-vous sûr de vouloir supprimer {{ $product->name }} ?"
                                class="px-3 py-1 text-xs font-bold text-white bg-red-500 rounded hover:bg-red-600 transition shadow-sm">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-zinc-500">Aucun produit en stock.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>

    {{-- MODALE D'AJOUT / MODIFICATION --}}
    @if($showingProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-900/50 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800">
                    <h2 class="text-xl font-bold text-zinc-800 dark:text-white">
                        {{ $editingProductId ? 'Modifier le produit' : 'Ajouter un nouveau fruit' }}
                    </h2>
                </div>
                
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    {{-- Nom --}}
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Nom du produit</label>
                        <input type="text" wire:model="name" class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Prix --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Prix (FCFA)</label>
                            <input type="number" wire:model="price" class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                        </div>
                        {{-- Stock --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Stock</label>
                            <input type="number" wire:model="stock" class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Poids --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Poids (kg)</label>
                            <input type="number" step="0.01" wire:model="weight" class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                        </div>
                        {{-- Unité --}}
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Unité</label>
                            <select wire:model="unit" class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white">
                                <option value="">Choisir...</option>
                                <option value="kg">kg</option>
                                <option value="sac">sac</option>
                                <option value="unité">unité</option>
                            </select>
                        </div>
                    </div>

                    {{-- Image --}}
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Lien de l'image (URL)</label>
                        <input type="text" wire:model="image_url" placeholder="https://..." class="w-full p-2.5 rounded-lg border border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    {{-- Boutons --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showingProductModal', false)" class="px-4 py-2 text-sm font-bold text-zinc-500 hover:text-zinc-700 transition">
                            Annuler
                        </button>
                        <button type="submit" class="px-6 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-lg shadow-lg transition">
                            {{ $editingProductId ? 'Enregistrer les modifications' : 'Confirmer l\'ajout' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
 @else
            <div class="bg-white p-10 rounded-xl border border-slate-200 text-center shadow-sm">
                <h2 class="text-xl font-bold mb-2">Bienvenue sur ARAME</h2>
                <p class="text-slate-500">Utilisez le menu pour passer vos commandes de produits frais.</p>
            </div>
    @endif