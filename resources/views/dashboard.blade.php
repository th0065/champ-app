<x-layouts.app :title="__('Admin Dashboard Home')">
    <div class="flex flex-col gap-6 p-6 bg-[#f8f9fa] min-h-screen text-slate-800">
        
        @if(auth()->user()->role === 'admin')
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-2xl font-bold text-slate-900">Admin Dashboard Home</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-slate-500 text-sm font-medium mb-2">Daily Sales</p>
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($stats['daily_sales'], 0, ',', ' ') }} FCFA</p>
                </div>

                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-slate-500 text-sm font-medium mb-2">Pending Orders</p>
                    <p class="text-3xl font-bold text-slate-900">{{ $stats['pending_orders'] }}</p>
                </div>

                <div class="bg-white p-8 rounded-xl border border-slate-200 shadow-sm text-center text-red-600">
                    <p class="text-slate-500 text-sm font-medium mb-2">Critical Stock</p>
                    <p class="text-3xl font-bold">{{ $stats['critical_stock'] }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <h2 class="text-lg font-bold">Tableau de ventes</h2>
                    <div class="flex bg-slate-100 p-1 rounded-lg">
                        <button class="px-4 py-1.5 bg-white shadow-sm rounded-md text-sm font-bold">Hebdomadaire</button>
                    </div>
                </div>
                
                <div class="h-64 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100">
                    <h2 class="text-lg font-bold">Driver Status</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                            <tr>
                                <th class="px-6 py-4">Driver Name</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @forelse($drivers as $driver)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-medium text-slate-700">{{ $driver->name }}</td>
                                <td class="px-6 py-4">
                                    @if(Cache::has('user-is-online-' . $driver->id))
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                            <span class="w-2 h-2 mr-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                            Online
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-500">
                                            Offline
                                        </td>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-slate-400">Aucun livreur enregistré avec le rôle "driver".</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: [{
                            label: 'Ventes (FCFA)',
                            data: {!! json_encode($salesData) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            </script>

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