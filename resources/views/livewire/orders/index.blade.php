  @if(auth()->user()->role === 'admin')

<div class="p-6 min-h-screen bg-zinc-50 dark:bg-zinc-950">
    @if(!$selectedOrder)
        {{-- ========================================== --}}
        {{--              VUE LISTE DES COMMANDES         --}}
        {{-- ========================================== --}}
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Commande Management</h1>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Filtres de statut --}}
            <div class="flex flex-wrap gap-2 p-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl w-fit shadow-sm">
                @foreach(['Tous', 'En attente', 'En cours', 'Livré', 'Annulé'] as $statusName)
                    <button 
                        wire:click="setFilter('{{ $statusName }}')"
                        class="px-4 py-2 text-sm font-bold rounded-lg transition-all {{ $filter === $statusName ? 'bg-zinc-800 text-white dark:bg-zinc-100 dark:text-zinc-900' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        {{ $statusName }}
                    </button>
                @endforeach
            </div>

            {{-- Tableau des commandes --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Commande N°</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Quartier</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-zinc-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($orders as $order)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors group">
                                <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">#{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">{{ $order->user->name ?? 'Client Inconnu' }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-500 italic">{{ $order->address ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-white">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = match($order->status) {
                                            'pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                            'delivered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                            'canceled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-zinc-100 text-zinc-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $statusStyles }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <button wire:click="showOrder({{ $order->id }})" class="px-4 py-1.5 text-xs font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition shadow-sm">
                                        Detail
                                    </button>
                                    
                                    <select 
                                        wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        class="text-[10px] font-bold border-zinc-200 dark:border-zinc-700 rounded-lg bg-zinc-50 dark:bg-zinc-800 dark:text-zinc-300 py-1 cursor-pointer outline-none">
                                        <option value="">Statut</option>
                                        <option value="pending">En attente</option>
                                        <option value="processing">En cours</option>
                                        <option value="delivered">Livré</option>
                                        <option value="canceled">Annulé</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-400 italic">Aucune commande trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        {{-- ========================================== --}}
        {{--           VUE DÉTAIL DE LA COMMANDE          --}}
        {{-- ========================================== --}}
        <div class="max-w-6xl mx-auto space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-300">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-zinc-800 dark:text-white">Détail de la Commande #{{ $selectedOrder->id }}</h1>
                <button wire:click="closeDetail" class="px-5 py-2 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl font-bold text-sm hover:bg-zinc-300 transition">
                    ← Retour à la liste
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Informations Client --}}
                <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                    <h2 class="font-bold text-zinc-400 text-xs uppercase tracking-widest mb-6 border-b border-zinc-50 dark:border-zinc-800 pb-2">Informations Client</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Nom Complet</label>
                            <p class="font-bold text-zinc-800 dark:text-white">{{ $selectedOrder->user->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">WhatsApp</label>
                            <p class="font-bold text-emerald-600 tracking-wide">{{ $selectedOrder->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-zinc-400 uppercase">Adresse de livraison</label>
                            <p class="font-medium text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $selectedOrder->address ?? 'N/A' }}</p>
                        </div>
                        
                        <a href="https://wa.me/{{ $selectedOrder->user->phone }}" target="_blank" class="mt-4 w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold text-sm transition-all shadow-md shadow-emerald-500/20">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.588-5.946 0-6.556 5.332-11.888 11.888-11.888 3.176 0 6.161 1.237 8.404 3.48s3.484 5.229 3.484 8.408c0 6.556-5.332 11.888-11.888 11.888-2.003 0-3.963-.505-5.7-1.467l-6.307 1.688z"/></svg>
                            Contacter le client
                        </a>
                    </div>
                </div>

                {{-- Détails de la Commande (Articles) --}}
                <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                    <h2 class="font-bold text-zinc-400 text-xs uppercase tracking-widest mb-6 border-b border-zinc-50 dark:border-zinc-800 pb-2">Résumé de la Commande</h2>
                    
                    {{-- Liste dynamique des items --}}
                    <div class="space-y-3 mb-6 max-h-48 overflow-y-auto pr-2">
                        @forelse($selectedOrder->items as $item)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-zinc-600 dark:text-zinc-400">• {{ $item->product_name ?? 'Produit' }} (x{{ $item->quantity }})</span>
                                <span class="font-bold dark:text-white">{{ number_format($item->price, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @empty
                            <p class="text-xs text-zinc-400 italic">Aucun article enregistré.</p>
                        @endforelse
                    </div>

                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 space-y-2">
                        <div class="flex justify-between text-xs text-zinc-500">
                            <span>Montant Total</span>
                            <span>{{ number_format($selectedOrder->total_amount, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between text-xs text-zinc-500 italic">
                            <span>Frais de livraison</span>
                            <span>{{ number_format($selectedOrder->delivery_fee ?? 0, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 text-lg font-black text-emerald-600">
                            <span>Total à payer</span>
                            <span>{{ number_format($selectedOrder->total_amount + ($selectedOrder->delivery_fee ?? 0), 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between text-[10px] text-zinc-400 font-bold uppercase">
                        <span>Date: {{ $selectedOrder->created_at->format('d/m/Y H:i') }}</span>
                        <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded">{{ $selectedOrder->status }}</span>
                    </div>
                </div>
            </div>

            {{-- Bloc Attribution Livreur --}}
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-100 dark:border-zinc-800 shadow-sm">
                <h2 class="font-bold text-zinc-800 dark:text-white mb-4">Assigner un Livreur</h2>
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <select wire:model="driver_id" class="w-full p-3 rounded-xl border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-200 outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Sélectionner un livreur disponible --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }} (Libre)</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="assignDriver" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-xl transition shadow-lg shadow-blue-500/20">
                        Assigner et Notifier
                    </button>
                </div>
            </div>

            {{-- Actions rapides du bas --}}
            <div class="flex flex-wrap gap-4 pt-4">
                <button wire:click="closeDetail" class="px-6 py-3 bg-zinc-200 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-bold rounded-xl">Retour</button>
                <button wire:click="updateStatus({{ $selectedOrder->id }}, 'canceled')" class="px-6 py-3 bg-red-100 text-red-700 hover:bg-red-200 font-bold rounded-xl transition">Annuler la Commande</button>
                <button wire:click="updateStatus({{ $selectedOrder->id }}, 'delivered')" class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-xl transition shadow-lg shadow-emerald-500/20">Marquer comme Livré</button>
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