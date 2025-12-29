<x-layouts.app :title="__('Admin Dashboard Home')">
    <div class="flex flex-col gap-6 p-6 bg-[#f8f9fa] min-h-screen text-slate-800">
       @if(auth()->user()->role === 'admin')
    <div class="flex flex-col gap-8">
        
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Admin Dashboard Home</h1>
            <div class="px-4 py-2 bg-white rounded-lg border border-slate-200 shadow-sm text-sm font-bold text-slate-600">
                {{ now()->format('d M Y') }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Ventes --}}
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center text-center transition hover:shadow-md">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Daily Sales</p>
                <p class="text-3xl font-black text-slate-900">{{ number_format($stats['daily_sales'], 0, ',', ' ') }} <span class="text-sm">FCFA</span></p>
            </div>

            {{-- Commandes --}}
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center text-center transition hover:shadow-md">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                </div>
                <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Pending Orders</p>
                <p class="text-3xl font-black text-slate-900">{{ $stats['pending_orders'] }}</p>
            </div>

            {{-- Stocks --}}
            <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center text-center transition hover:shadow-md border-b-4 border-b-red-500">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <p class="text-slate-500 text-xs font-black uppercase tracking-widest mb-1">Critical Stock</p>
                <p class="text-3xl font-black text-red-600">{{ $stats['critical_stock'] }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h2 class="text-lg font-black text-slate-800 uppercase">Tableau de ventes</h2>
                <div class="flex bg-slate-100 p-1 rounded-xl">
                    <button class="px-4 py-1.5 bg-white shadow-sm rounded-lg text-xs font-black text-blue-600 uppercase">Hebdomadaire</button>
                </div>
            </div>
            <div class="h-72 w-full">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-lg font-black text-slate-800 uppercase">Driver Status</h2>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live monitoring</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-500 text-[10px] uppercase font-black tracking-widest border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Livreur</th>
                            <th class="px-6 py-4">Connexion App</th>
                            <th class="px-6 py-4 text-center">Service Livraison</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm font-medium">
                        @forelse($drivers as $driver)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $driver->name }}</td>
                            <td class="px-6 py-4">
                                @if(Cache::has('user-is-online-' . $driver->id))
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-green-100 text-green-700">
                                        Online
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-400">
                                        Offline
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($driver->is_available)
                                    <span class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase shadow-sm">
                                        🚀 En Service
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-400 rounded-lg text-[10px] font-black uppercase">
                                        Indisponible
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400">Aucun livreur enregistré.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: [{
                            label: 'Ventes (FCFA)',
                            data: {!! json_encode($salesData) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            borderWidth: 4,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { weight: 'bold' } }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { font: { weight: 'bold' } }
                            }
                        }
                    }
                });
            });
        </script>
    </div>


       @elseif(auth()->user()->role === 'driver')
           <div class="max-w-lg mx-auto w-full">
                 @livewire('driver.dashboard-manager')
            </div>
       @elseif(auth()->user()->role === 'buyer')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <h2 class="text-2xl font-bold mb-2 text-slate-900">Ravi de vous revoir, {{ auth()->user()->name }} !</h2>
            <p class="text-slate-500 mb-6">Suivez vos commandes de produits frais en un clin d'œil.</p>
            <a href="/" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition">
                Faire de nouvelles courses
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100">
                <p class="text-blue-600 text-xs font-black uppercase mb-1">Commandes</p>
                <p class="text-3xl font-black text-blue-900">{{ auth()->user()->orders()->count() }}</p>
            </div>
            <div class="bg-green-50 p-6 rounded-2xl border border-green-100">
                <p class="text-green-600 text-xs font-black uppercase mb-1">Livré</p>
                <p class="text-3xl font-black text-green-900">{{ auth()->user()->orders()->where('status', 'delivered')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="mt-8">
         <h3 class="font-bold text-lg mb-4">Commandes récentes</h3>
         @livewire('buyer.recent-orders') {{-- Si vous créez ce composant --}}
    </div>
@endif
    </div>
</x-layouts.app>