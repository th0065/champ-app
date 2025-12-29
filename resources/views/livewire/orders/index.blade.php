{{-- UNE SEULE DIV RACINE --}}
<div class="p-8 bg-white min-h-screen">

    @if(auth()->user()->role === 'admin')
        @if(!$selectedOrder)
            {{-- ========================================== --}}
            {{--           VUE LISTE DES COMMANDES          --}}
            {{-- ========================================== --}}
            
            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
                <div>
                    <h1 class="text-4xl font-black text-black uppercase tracking-tighter mb-2">
                        Gestion des Commandes
                    </h1>
                    <p class="text-zinc-500 font-bold text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Flux d'activité : <span class="text-black font-black">Temps réel</span>
                    </p>
                </div>
            </div>

            {{-- FILTRES --}}
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach(['Tous', 'En attente', 'En cours', 'Livré', 'Annulé'] as $statusName)
                    <button 
                        wire:click="setFilter('{{ $statusName }}')"
                        class="px-6 py-2.5 text-[11px] font-black uppercase tracking-widest rounded-xl transition-all border {{ $filter === $statusName ? 'bg-black text-white border-black shadow-xl shadow-zinc-200' : 'bg-white text-zinc-400 border-zinc-100 hover:border-zinc-300' }}">
                        {{ $statusName }}
                    </button>
                @endforeach
            </div>

            {{-- TABLEAU --}}
            <div class="bg-white rounded-[2rem] border border-zinc-200 shadow-2xl shadow-zinc-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-zinc-50 border-b border-zinc-100">
                        <tr>
                            <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">N° Ref</th>
                            <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Client</th>
                            <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Localisation</th>
                            <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest text-center">Montant</th>
                            <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest text-center">État</th>
                            <th class="px-8 py-6 text-right text-black font-black text-[11px] uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 italic">
                        @forelse($orders as $order)
                            <tr class="hover:bg-zinc-50/50 transition-colors group">
                                <td class="px-8 py-5 not-italic">
                                    <span class="font-black text-black">#{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-8 py-5 not-italic">
                                    <span class="font-bold text-black block">{{ $order->user->name ?? 'Client Inconnu' }}</span>
                                    <span class="text-[10px] text-zinc-400 uppercase font-black tracking-tight">{{ $order->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-8 py-5 text-sm text-zinc-500 font-medium">
                                    {{ Str::limit($order->address, 30) ?? 'N/A' }}
                                </td>
                                <td class="px-8 py-5 text-center not-italic">
                                    <span class="font-black text-black text-base">{{ number_format($order->total_amount, 0, ',', ' ') }}</span>
                                    <span class="text-[10px] text-zinc-400 font-bold uppercase">FCFA</span>
                                </td>
                                <td class="px-8 py-5 text-center not-italic">
                                    @php
                                        $statusStyles = match($order->status) {
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'delivered' => 'bg-emerald-600 text-white',
                                            'processing' => 'bg-blue-600 text-white',
                                            'canceled' => 'bg-zinc-100 text-zinc-400',
                                            default => 'bg-zinc-100 text-zinc-700'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter {{ $statusStyles }}">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right not-italic">
                                    <div class="flex justify-end items-center gap-3">
                                        <button wire:click="showOrder({{ $order->id }})" class="bg-black text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-zinc-800 transition shadow-lg shadow-zinc-200">
                                            Détails
                                        </button>
                                        <select 
                                            wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                            class="text-[10px] font-black border-none rounded-xl bg-zinc-100 py-2 cursor-pointer outline-none focus:ring-2 focus:ring-black">
                                            <option value="">Status</option>
                                            <option value="pending">Attente</option>
                                            <option value="processing">En cours</option>
                                            <option value="delivered">Livré</option>
                                            <option value="canceled">Annulé</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <p class="text-zinc-400 font-black uppercase text-xs tracking-widest">Aucune commande répertoriée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @else
            {{-- ========================================== --}}
            {{--           VUE DÉTAIL DE LA COMMANDE          --}}
            {{-- ========================================== --}}
            <div class="max-w-5xl mx-auto animate-in fade-in slide-in-from-bottom-6 duration-500">
                <div class="flex justify-between items-center mb-10">
                    <button wire:click="closeDetail" class="group flex items-center gap-3 text-black font-black uppercase text-xs tracking-widest">
                        <span class="w-10 h-10 rounded-full bg-zinc-100 flex items-center justify-center group-hover:bg-black group-hover:text-white transition-all">←</span>
                        Retour à la liste
                    </button>
                    <div class="text-right">
                        <h2 class="text-3xl font-black text-black uppercase tracking-tighter">Commande #{{ $selectedOrder->id }}</h2>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ $selectedOrder->created_at->format('d F Y à H:i') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- COLONNE GAUCHE : CLIENT --}}
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white p-8 rounded-[2rem] border border-zinc-200 shadow-xl shadow-zinc-100">
                            <h3 class="font-black text-black text-[11px] uppercase tracking-widest mb-6 pb-2 border-b border-zinc-50">Client</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Nom</label>
                                    <p class="font-black text-black text-lg">{{ $selectedOrder->user->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">WhatsApp</label>
                                    <p class="font-black text-blue-600 text-lg tracking-tight">{{ $selectedOrder->user->phone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-[9px] font-black text-zinc-400 uppercase block mb-1">Adresse</label>
                                    <p class="font-bold text-zinc-600 leading-relaxed italic">" {{ $selectedOrder->address ?? 'N/A' }} "</p>
                                </div>
                                <a href="https://wa.me/{{ $selectedOrder->user->phone }}" target="_blank" 
                                    class="w-full flex items-center justify-center gap-3 px-6 py-4 bg-[#25D366] text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:brightness-110 transition-all shadow-lg shadow-emerald-100">
                                    Contacter via WhatsApp
                                </a>
                            </div>
                        </div>

                        {{-- ASSIGNATION LIVREUR --}}
                        <div class="bg-black p-8 rounded-[2rem] text-white shadow-2xl shadow-zinc-200">
                            <h3 class="font-black text-zinc-400 text-[11px] uppercase tracking-widest mb-6">Logistique</h3>
                            <div class="space-y-4">
                                <select wire:model="driver_id" class="w-full p-4 rounded-xl bg-zinc-900 border-none text-xs font-black uppercase tracking-tight focus:ring-2 focus:ring-blue-600">
                                    <option value="">Sélectionner Livreur</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                    @endforeach
                                </select>
                                <button wire:click="assignDriver" class="w-full py-4 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all">
                                    Assigner & Notifier
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- COLONNE DROITE : ARTICLES --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white p-8 rounded-[2rem] border border-zinc-200 shadow-xl shadow-zinc-100 h-full">
                            <h3 class="font-black text-black text-[11px] uppercase tracking-widest mb-6 pb-2 border-b border-zinc-50">Panier</h3>
                            
                            <div class="space-y-4 mb-10">
                                @foreach($selectedOrder->items as $item)
                                    <div class="flex justify-between items-center py-4 border-b border-zinc-50 group hover:bg-zinc-50 px-4 rounded-xl transition-colors">
                                        <div class="flex items-center gap-4">
                                            <span class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center text-[10px] font-black">{{ $item->quantity }}x</span>
                                            <span class="font-black text-black uppercase text-sm tracking-tight">{{ $item->product_name }}</span>
                                        </div>
                                        <span class="font-black text-black">{{ number_format($item->price, 0, ',', ' ') }} <span class="text-[10px] text-zinc-400">FCFA</span></span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="space-y-3 bg-zinc-50 p-8 rounded-3xl">
                                <div class="flex justify-between text-xs font-bold text-zinc-500 uppercase">
                                    <span>Sous-total</span>
                                    <span>{{ number_format($selectedOrder->total_amount, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between text-xs font-bold text-zinc-500 uppercase border-b border-zinc-200 pb-4">
                                    <span>Livraison</span>
                                    <span>{{ number_format($selectedOrder->delivery_fee ?? 0, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-xs font-black text-black uppercase tracking-widest">Total Net</span>
                                    <span class="text-3xl font-black text-black">{{ number_format($selectedOrder->total_amount + ($selectedOrder->delivery_fee ?? 0), 0, ',', ' ') }} <span class="text-sm">FCFA</span></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-8">
                                <button wire:click="updateStatus({{ $selectedOrder->id }}, 'canceled')" class="py-4 border border-zinc-200 text-zinc-400 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all">Annuler</button>
                                <button wire:click="updateStatus({{ $selectedOrder->id }}, 'delivered')" class="py-4 bg-black text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-zinc-800 transition-all shadow-xl shadow-zinc-200">Terminer la livraison</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        {{-- VUE CLIENT --}}
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="bg-white p-12 rounded-[2.5rem] border border-zinc-100 text-center shadow-2xl shadow-zinc-100 max-w-md">
                <div class="text-5xl mb-6">🛒</div>
                <h2 class="text-3xl font-black text-black uppercase tracking-tighter mb-4">Espace Client</h2>
                <p class="text-zinc-500 font-bold leading-relaxed mb-8">Utilisez le menu pour commander vos produits frais directement chez vous.</p>
                <button class="bg-black text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest">Nouvelle Commande</button>
            </div>
        </div>
    @endif
</div>