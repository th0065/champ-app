{{-- UNE SEULE DIV RACINE --}}
<div class="p-8 bg-white min-h-screen">

    @if(auth()->user()->role === 'admin')
        {{-- HEADER : TITRE ET BOUTON AJOUTER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-black text-black uppercase tracking-tighter mb-2">
                    Gestion du Stock
                </h1>
                <p class="text-zinc-500 font-bold text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    Inventaire : <span class="text-black font-black">{{ $products->total() }}</span> références actives
                </p>
            </div>

            <button wire:click="openModal" class="bg-black hover:bg-zinc-800 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 transition-all shadow-xl shadow-zinc-200 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                Ajouter un Produit
            </button>
        </div>

        {{-- BARRE DE RECHERCHE --}}
        <div class="mb-8 relative max-w-2xl">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un produit (orange, mangue, sac...)" 
                class="w-full rounded-2xl border-zinc-200 text-sm pl-12 py-4 focus:ring-black focus:border-black shadow-sm placeholder-zinc-400 font-medium text-black bg-zinc-50/50">
            <span class="absolute left-4 top-4.5 text-zinc-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </span>
        </div>

        {{-- TABLEAU DES PRODUITS --}}
        <div class="bg-white rounded-[2rem] border border-zinc-200 shadow-2xl shadow-zinc-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 border-b border-zinc-100">
                    <tr>
                        <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Aperçu</th>
                        <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Désignation</th>
                        <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest text-center">Prix & Unité</th>
                        <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest text-center">Stock Actuel</th>
                        <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest text-center">Statut</th>
                        <th class="px-8 py-6 text-right text-black font-black text-[11px] uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 italic">
                    @forelse($products as $product)
                    <tr class="hover:bg-zinc-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="w-14 h-14 rounded-2xl bg-zinc-100 border border-zinc-200 overflow-hidden flex items-center justify-center">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                @else
                                    <span class="text-zinc-400 text-xs font-black not-italic">NO IMG</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="font-black text-black text-base uppercase tracking-tight not-italic">{{ $product->name }}</span>
                            <p class="text-[10px] text-zinc-400 not-italic font-bold">{{ $product->weight }} kg / {{ $product->unit }}</p>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="font-black text-black not-italic">{{ number_format($product->price, 0, ',', ' ') }}</span>
                            <span class="text-[10px] text-zinc-400 font-bold uppercase not-italic">FCFA</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="text-lg font-black text-black not-italic">{{ $product->stock }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $status = match(true) {
                                    $product->stock <= 20 => ['label' => 'Critique', 'class' => 'bg-red-600 text-white'],
                                    $product->stock <= 100 => ['label' => 'Faible', 'class' => 'bg-amber-500 text-white'],
                                    default => ['label' => 'Optimal', 'class' => 'bg-black text-white'],
                                };
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter {{ $status['class'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right space-x-2 not-italic">
                            <button wire:click="editProduct({{ $product->id }})" class="p-2 text-zinc-400 hover:text-blue-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                            </button>
                            <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Supprimer ce produit ?" class="p-2 text-zinc-300 hover:text-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <p class="text-zinc-400 font-black uppercase text-xs tracking-widest">Aucun produit en stock</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-8 py-6 border-t border-zinc-100 bg-zinc-50/30">
                {{ $products->links() }}
            </div>
        </div>

        {{-- MODALE PRODUIT --}}
        @if($showingProductModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
            <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-zinc-200">
                <div class="p-10">
                    <h3 class="text-2xl font-black text-black uppercase tracking-tighter mb-8">
                        {{ $editingProductId ? 'Modifier le produit' : 'Nouveau Produit' }}
                    </h3>
                    
                    <form wire:submit.prevent="save" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Désignation du fruit</label>
                            <input type="text" wire:model="name" class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Prix (FCFA)</label>
                                <input type="number" wire:model="price" class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Stock Initial</label>
                                <input type="number" wire:model="stock" class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Poids (kg)</label>
                                <input type="number" step="0.01" wire:model="weight" class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Unité de mesure</label>
                                <select wire:model="unit" class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                                    <option value="">Choisir...</option>
                                    <option value="kg">kilogramme (kg)</option>
                                    <option value="sac">sac</option>
                                    <option value="unité">unité</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">URL de l'image</label>
                            <input type="text" wire:model="image_url" placeholder="https://..." class="w-full rounded-2xl border-none p-4 bg-zinc-50 text-sm font-bold focus:ring-black shadow-inner">
                        </div>

                        <div class="flex gap-4 pt-6">
                            <button type="button" wire:click="$set('showingProductModal', false)" 
                                class="flex-1 px-6 py-4 rounded-2xl border border-zinc-200 text-xs font-black uppercase text-black hover:bg-zinc-50 transition">
                                Annuler
                            </button>
                            <button type="submit" 
                                class="flex-1 px-6 py-4 rounded-2xl bg-black text-white text-xs font-black uppercase hover:bg-zinc-800 transition shadow-xl shadow-zinc-200">
                                {{ $editingProductId ? 'Mettre à jour' : 'Enregistrer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

    @else
        {{-- VUE CLIENT --}}
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="bg-white p-12 rounded-[2.5rem] border border-zinc-100 text-center shadow-2xl shadow-zinc-100 max-w-md">
                <div class="text-5xl mb-6">🌿</div>
                <h2 class="text-3xl font-black text-black uppercase tracking-tighter mb-4">Bienvenue sur ARAME</h2>
                <p class="text-zinc-500 font-bold leading-relaxed">Parcourez notre catalogue et commandez vos produits frais en quelques clics.</p>
            </div>
        </div>
    @endif
</div>