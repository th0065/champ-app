  @if(auth()->user()->role === 'admin')
<div class="p-6 space-y-8">
    {{-- 1. COMMANDES EN ATTENTE D'ATTRIBUTION --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
            <h2 class="font-bold text-zinc-700 dark:text-zinc-300">Commandes en attente d'attribution</h2>
        </div>
        <table class="w-full text-left border-collapse">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-[11px] font-bold text-zinc-500 uppercase">
                <tr>
                    <th class="px-6 py-3">ID Commande</th>
                    <th class="px-6 py-3">Client</th>
                    <th class="px-6 py-3">Quartier</th>
                    <th class="px-6 py-3">Montant</th>
                    <th class="px-6 py-3">Assigner à</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @foreach($pendingOrders as $order)
                <tr class="text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                    <td class="px-6 py-4 font-bold">#{{ $order->id }}</td>
                    <td class="px-6 py-4">{{ $order->user->name ?? 'Client' }}</td>
                    {{-- On utilise la colonne 'address' de votre table orders --}}
                    <td class="px-6 py-4 text-zinc-500">{{ $order->address ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-semibold text-emerald-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4">
                        <select wire:model="selectedDrivers.{{ $order->id }}" class="text-xs p-1.5 rounded-md border border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700">
                            <option value="">Choisir livreur...</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="assignOrder({{ $order->id }})" 
                                class="px-4 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded font-bold text-xs transition shadow-sm">
                            Attribuer
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 2. SECTION INTERMÉDIAIRE : SUIVI & ÉTAT --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Suivi des Livraisons en cours --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-zinc-400 uppercase mb-4">Suivi des Livraisons en cours</h2>
            <div class="space-y-6">
                @forelse($ongoingDeliveries as $delivery)
                <div class="space-y-2">
                    <div class="flex justify-between text-[11px]">
                        <span class="font-bold text-zinc-700 dark:text-white">Commande #{{ $delivery->order_id }} ({{ $delivery->driver->name }})</span>
                        <span class="text-zinc-500 italic text-[10px]">Départ: {{ $delivery->assigned_at->format('H:i') }}</span>
                    </div>
                    {{-- Barre de progression simulée --}}
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-full animate-pulse" style="width: 45%"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-zinc-400 italic text-center py-4">Aucune livraison en cours de trajet.</p>
                @endforelse
            </div>
        </div>

        {{-- État des Livreurs --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm">
            <h2 class="text-xs font-bold text-zinc-400 uppercase mb-4">État des Livreurs</h2>
            <div class="grid grid-cols-1 gap-3">
                @foreach($drivers as $driver)
                <div class="flex items-center gap-3 text-[11px]">
                    <span class="w-2.5 h-2.5 rounded-full {{ $driver->status === 'Libre' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                    <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $driver->name }} :</span>
                    <span class="text-zinc-500">{{ $driver->status ?? 'Libre' }} (Dernière position connue)</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- 3. CARTE DE SUIVI --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm">
        <h2 class="font-bold text-zinc-700 dark:text-zinc-300 text-center mb-6">Carte de Suivi Temps Réel</h2>
        <div class="w-full h-64 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border-2 border-dashed border-zinc-200 dark:border-zinc-700 flex flex-col items-center justify-center relative overflow-hidden">
             {{-- Icônes de décoration pour simuler la carte comme sur l'image --}}
             <div class="absolute top-10 left-1/4 w-4 h-4 border-2 border-red-500 rounded-sm"></div>
             <div class="absolute bottom-10 right-1/4 w-4 h-4 border-2 border-red-500 rounded-sm"></div>
             <svg class="w-12 h-12 text-blue-500 mb-2 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M10 17l5-5-5-5v10z"/></svg>
             <p class="text-xs text-zinc-400 italic font-medium">Visualisation des trajets en temps réel</p>
        </div>
    </div>
</div>
 @else
            <div class="bg-white p-10 rounded-xl border border-slate-200 text-center shadow-sm">
                <h2 class="text-xl font-bold mb-2">Bienvenue sur ARAME</h2>
                <p class="text-slate-500">Utilisez le menu pour passer vos commandes de produits frais.</p>
            </div>
    @endif