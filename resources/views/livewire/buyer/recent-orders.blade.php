<div class="space-y-4">
    @forelse($orders as $order)
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:border-blue-200">
            
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-slate-100 rounded-xl flex items-center justify-center text-xl">
                    📦
                </div>
                <div>
                    <h4 class="font-bold text-slate-900">Commande #{{ $order->id }}</h4>
                    <p class="text-xs text-slate-500 font-medium">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <div>
                @php
                    $statusStyles = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'processing' => 'bg-blue-100 text-blue-700',
                        'on_delivery' => 'bg-orange-100 text-orange-700 animate-pulse',
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                    $labels = [
                        'pending' => 'En attente',
                        'processing' => 'Préparation',
                        'on_delivery' => 'En livraison',
                        'delivered' => 'Livré',
                        'cancelled' => 'Annulé',
                    ];
                @endphp
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusStyles[$order->status] ?? 'bg-slate-100 text-slate-600' }}">
                    {{ $labels[$order->status] ?? $order->status }}
                </span>
            </div>

            <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-0 pt-4 md:pt-0">
                <div class="text-right">
                    <p class="text-sm font-black text-slate-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                    <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $order->payment_method }}</p>
                </div>
                
                <a href="#" class="p-2 hover:bg-slate-50 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-10 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
            <p class="text-slate-400 font-medium">Aucune commande récente.</p>
        </div>
    @endforelse
</div>