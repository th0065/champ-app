<div class="w-full max-w-2xl mx-auto pb-10">
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl font-bold text-center shadow-sm animate-bounce">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden min-h-[600px] flex flex-col">
        
        <div class="p-6 border-b border-slate-100 text-center relative bg-white">
            @if($view === 'details')
                <button wire:click="goBack" class="absolute left-6 top-7 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
            @endif
            <h1 class="text-xl font-bold text-slate-800 tracking-tight tracking-widest">DRIVER PANEL</h1>
        </div>

        <div class="p-6 flex-1">
            @if($view === 'list')
                <div class="flex justify-between items-center mb-8 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                    <div>
                        <h2 class="font-bold text-slate-800">Votre Statut</h2>
                        <p class="text-xs {{ $isAvailable ? 'text-green-600' : 'text-slate-400' }} font-bold uppercase">
                            {{ $isAvailable ? '● En service' : '○ Hors service' }}
                        </p>
                    </div>
                    <button wire:click="toggleAvailability" 
                        class="w-14 h-7 rounded-full transition-all relative {{ $isAvailable ? 'bg-green-500' : 'bg-slate-300' }}">
                        <div class="absolute top-1 bg-white w-5 h-5 rounded-full transition-all {{ $isAvailable ? 'left-8' : 'left-1' }}"></div>
                    </button>
                </div>

                <div class="space-y-4">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        Vos Livraisons <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-lg text-sm">{{ $deliveries->count() }}</span>
                    </h3>
                    
                    @forelse($deliveries as $delivery)
                        <div class="border border-slate-200 rounded-2xl p-5 bg-white shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="font-black text-slate-900 text-lg">#{{ $delivery->order_id }}</p>
                                    <p class="text-slate-500 text-sm italic">{{ $delivery->order->shipping_zone ?? 'Dakar' }}</p>
                                </div>
                                <div class="text-right">
                                    @if(strtolower($delivery->order->payment_method) === 'cash')
                                        <p class="font-bold text-orange-600">{{ number_format($delivery->order->total_amount, 0, ',', ' ') }} <span class="text-[10px]">FCFA</span></p>
                                        <span class="text-[9px] font-black bg-orange-100 text-orange-600 px-2 py-0.5 rounded uppercase">À Encaisser</span>
                                    @else
                                        <p class="font-bold text-green-600 uppercase">Payé</p>
                                        <span class="text-[9px] font-black bg-green-100 text-green-600 px-2 py-0.5 rounded uppercase">{{ $delivery->order->payment_method }}</span>
                                    @endif
                                </div>
                            </div>
                            <button wire:click="showDetails({{ $delivery->id }})" class="w-full py-4 bg-slate-900 text-white font-bold rounded-xl hover:bg-blue-600 transition">
                                Détails Livraison
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-20 text-slate-300 italic font-medium">Aucune livraison pour le moment.</div>
                    @endforelse
                </div>

            @else
                <div class="space-y-6">
                    <section class="bg-slate-900 text-white rounded-2xl p-5 shadow-xl">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Total Commande</h3>
                                <p class="text-3xl font-black">{{ number_format($current->order->total_amount, 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] block mb-1 opacity-60 uppercase font-bold">Méthode</span>
                                <span class="bg-white/20 px-3 py-1 rounded-lg font-bold text-sm uppercase">
                                    {{ $current->order->payment_method }}
                                </span>
                            </div>
                        </div>

                        @if(strtolower($current->order->payment_method) === 'cash')
                            <div class="mt-4 py-3 bg-orange-500 text-white text-center rounded-xl font-black text-sm animate-pulse uppercase">
                                ⚠️ Encaisser l'argent au client
                            </div>
                        @else
                            <div class="mt-4 py-3 bg-green-500 text-white text-center rounded-xl font-black text-sm uppercase">
                                ✓ Commande déjà réglée
                            </div>
                        @endif
                    </section>

                    <section class="space-y-3">
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider">Destinataire</h3>
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <p class="flex justify-between"><span class="text-slate-500">Nom:</span> <span class="font-bold">{{ $current->order->user->name }}</span></p>
                            <p class="flex justify-between mt-2"><span class="text-slate-500">Adresse:</span> <span class="font-bold text-right ml-4">{{ $current->order->shipping_address }}</span></p>
                        </div>
                    </section>

                    <div class="grid grid-cols-2 gap-4">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $current->order->user->phone) }}" target="_blank" class="flex items-center justify-center py-4 bg-[#25D366] text-white font-bold rounded-2xl shadow-lg shadow-green-100 italic text-lg">WhatsApp</a>
                        
                        {{-- Navigation GPS intelligente --}}
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $current->order->latitude }},{{ $current->order->longitude }}&travelmode=driving" 
                           target="_blank" 
                           class="flex items-center justify-center py-4 bg-blue-500 text-white font-bold rounded-2xl shadow-lg shadow-blue-100 text-lg">
                           GPS Route
                        </a>
                    </div>

                    <section>
                        <h3 class="font-bold text-slate-800 text-sm uppercase tracking-wider mb-3">Articles</h3>
                        <div class="border border-slate-100 rounded-2xl p-4 bg-white">
                            @foreach($current->order->items as $item)
                                <div class="flex justify-between py-2 border-b border-slate-50 last:border-0">
                                    <span class="text-slate-700">{{ $item->product->name }}</span>
                                    <span class="font-black text-slate-900">x{{ $item->quantity }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <div class="pt-4 border-t border-slate-100">
                        <textarea wire:model="comment" class="w-full border-2 border-slate-100 rounded-2xl p-4 mb-4 focus:border-green-500 outline-none transition-all" rows="2" placeholder="Note de livraison (optionnel)..."></textarea>
                        
                        <button wire:click="confirmDelivery" 
                                class="w-full py-5 bg-green-600 text-white font-black text-xl rounded-2xl shadow-xl hover:bg-green-700 transform active:scale-95 transition-all uppercase tracking-widest">
                            Livraison Terminée
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>