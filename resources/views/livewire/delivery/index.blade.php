{{-- UNE SEULE DIV RACINE POUR TOUT LE COMPOSANT --}}
<div class="min-h-screen bg-zinc-50">

    @if(auth()->user()->role === 'admin')
        <div class="p-6 space-y-8">
            
            <div class="border-l-8 border-blue-600 pl-6 py-2">
                <h1 class="text-4xl font-black text-black uppercase tracking-tight leading-none">
                    Gestion des Livraisons
                </h1>
                <p class="text-slate-600 font-bold text-base mt-2">
                    <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs uppercase font-black">Live</span>
                    Suivi de la flotte et attribution des commandes
                </p>
            </div>

            {{-- 1. COMMANDES EN ATTENTE D'ATTRIBUTION --}}
            <div class="bg-white border border-zinc-200 rounded-2xl shadow-sm overflow-hidden font-sans">
                <div class="p-4 border-b border-zinc-100 bg-zinc-50/50">
                    <h2 class="font-black text-black uppercase text-xs tracking-widest">Commandes en attente d'attribution</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-zinc-50 text-[11px] font-bold text-zinc-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-black">ID</th>
                                <th class="px-6 py-3">Client</th>
                                <th class="px-6 py-3">Quartier</th>
                                <th class="px-6 py-3">Montant</th>
                                <th class="px-6 py-3">Livreur (En Service)</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 italic">
                            @foreach($pendingOrders as $order)
                            <tr class="text-sm hover:bg-zinc-50 transition">
                                <td class="px-6 py-4 font-black text-blue-600">#{{ $order->id }}</td>
                                <td class="px-6 py-4 font-bold text-zinc-700">{{ $order->user->name ?? 'Client' }}</td>
                                <td class="px-6 py-4 text-zinc-500">{{ $order->address ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-black text-emerald-600">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="px-6 py-4">
                                    {{-- Seuls les livreurs disponibles (is_available = true) s'affichent ici --}}
                                    <select wire:model="selectedDrivers.{{ $order->id }}" class="text-xs p-2 rounded-xl border-zinc-200 focus:ring-blue-500 w-full font-bold">
                                        <option value="">Choisir livreur...</option>
                                        @foreach($availableDrivers as $driver)
                                            <option value="{{ $driver->id }}">🚀 {{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="assignOrder({{ $order->id }})" 
                                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-[10px] uppercase transition shadow-md shadow-blue-100">
                                        Attribuer
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. SECTION INTERMÉDIAIRE : SUIVI & ÉTAT --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Suivi des Livraisons en cours --}}
                <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xs font-black text-zinc-400 uppercase mb-6 tracking-widest">Livraisons en cours</h2>
                    <div class="space-y-6">
                        @forelse($ongoingDeliveries as $delivery)
                        <div class="space-y-2 p-3 bg-zinc-50 rounded-xl border border-zinc-100">
                            <div class="flex justify-between text-[11px]">
                                <span class="font-black text-black uppercase">Commande #{{ $delivery->order_id }}</span>
                                <span class="text-blue-600 font-bold uppercase">{{ $delivery->driver->name }}</span>
                            </div>
                            <div class="w-full bg-zinc-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-600 h-full animate-pulse" style="width: 45%"></div>
                            </div>
                            <p class="text-[9px] text-zinc-400 font-bold italic uppercase text-right">Assigné à {{ $delivery->created_at->format('H:i') }}</p>
                        </div>
                        @empty
                        <p class="text-xs text-zinc-400 italic text-center py-4 font-bold uppercase">Aucun trajet en cours.</p>
                        @endforelse
                    </div>
                </div>

                {{-- État des Livreurs (Gestion Disponibilité) --}}
                <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-xs font-black text-zinc-400 uppercase mb-6 tracking-widest">État de la flotte</h2>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($drivers as $driver)
                        <div class="flex items-center justify-between p-3 rounded-xl {{ $driver->is_available ? 'bg-emerald-50 border border-emerald-100' : 'bg-zinc-50 border border-zinc-100' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full {{ $driver->is_available ? 'bg-emerald-500 animate-pulse' : 'bg-zinc-300' }}"></span>
                                <span class="font-black text-xs text-zinc-800 uppercase">{{ $driver->name }}</span>
                            </div>
                            <button wire:click="toggleDriverAvailability({{ $driver->id }})" 
                                class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg border {{ $driver->is_available ? 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-zinc-200 text-zinc-500 border-zinc-300' }}">
                                {{ $driver->is_available ? 'En Service' : 'Hors Service' }}
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. CARTE DE SUIVI --}}
            <div class="bg-white border border-zinc-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xs font-black text-zinc-400 uppercase mb-4 tracking-widest">Carte de Suivi Temps Réel</h2>
                <div wire:ignore id="map" class="w-full h-80 bg-zinc-100 rounded-2xl border border-zinc-200 z-0"></div>
            </div>
        </div>

    @else
        {{-- VUE CLIENT --}}
        <div class="p-12 text-center">
            <div class="max-w-md mx-auto bg-white p-10 rounded-3xl border border-zinc-200 shadow-sm">
                <div class="text-4xl mb-4">📦</div>
                <h2 class="text-2xl font-black text-black uppercase mb-2">Bienvenue sur ARAME</h2>
                <p class="text-slate-500 font-medium">Utilisez le menu pour commander vos produits frais.</p>
            </div>
        </div>
    @endif

    {{-- SCRIPTS (À l'intérieur de la div racine) --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            const mapElement = document.getElementById('map');
            if (mapElement) {
                // Initialisation de la carte (Dakar par défaut)
                const map = L.map('map').setView([14.769, -17.394], 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Marqueurs pour les commandes en attente
                @foreach($pendingOrders as $order)
                    @if($order->latitude && $order->longitude)
                        L.marker([{{ $order->latitude }}, {{ $order->longitude }}])
                         .addTo(map)
                         .bindPopup('<b>Commande #{{ $order->id }}</b><br>{{ $order->address }}');
                    @endif
                @endforeach
            }
        });
    </script>
</div>