{{-- UNE SEULE DIV RACINE --}}
<div class="p-8 bg-white min-h-screen">

    {{-- HEADER : TITRE ET ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black text-black uppercase tracking-tighter mb-2">
                Gestion des Livreurs
            </h1>
            <p class="text-zinc-500 font-bold text-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                Effectif total : <span class="text-black font-black">{{ $drivers->total() }}</span> livreur(s) répertorié(s)
            </p>
        </div>
        
        <div class="flex gap-3 w-full md:w-auto">
            {{-- RECHERCHE --}}
            <div class="relative flex-1 md:w-72">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un nom ou email..." 
                    class="w-full rounded-2xl border-zinc-200 text-sm pl-10 py-3 focus:ring-black focus:border-black shadow-sm placeholder-zinc-400 font-medium text-black">
                <span class="absolute left-3 top-3.5 text-zinc-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </span>
            </div>
            
            {{-- BOUTON AJOUTER --}}
            <button wire:click="openModal" class="bg-black hover:bg-zinc-800 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-2 transition-all shadow-xl shadow-zinc-200 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" /></svg>
                Nouveau Livreur
            </button>
        </div>
    </div>

    {{-- ALERTES --}}
    @if (session()->has('message'))
        <div class="mb-8 p-4 bg-zinc-900 text-white rounded-2xl font-bold text-sm flex items-center gap-3 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- TABLEAU DES LIVREURS --}}
    <div class="bg-white rounded-[2rem] border border-zinc-200 shadow-2xl shadow-zinc-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-zinc-50 border-b border-zinc-100">
                <tr>
                    <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Identité du Livreur</th>
                    <th class="px-8 py-6 text-center text-black font-black text-[11px] uppercase tracking-widest">État du Service</th>
                    <th class="px-8 py-6 text-black font-black text-[11px] uppercase tracking-widest">Date d'inscription</th>
                    <th class="px-8 py-6 text-right text-black font-black text-[11px] uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 italic">
                @forelse($drivers as $driver)
                <tr class="hover:bg-zinc-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-black flex items-center justify-center font-black text-white shadow-inner">
                                {{ substr($driver->name, 0, 2) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-black text-black text-base">{{ $driver->name }}</span>
                                <span class="text-xs text-zinc-400 not-italic font-bold">{{ $driver->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <button wire:click="toggleAvailability({{ $driver->id }})" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase transition-all shadow-sm {{ $driver->is_available ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-zinc-100 text-zinc-400 hover:bg-zinc-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $driver->is_available ? 'bg-white' : 'bg-zinc-400' }}"></span>
                            {{ $driver->is_available ? 'En Service' : 'Hors Service' }}
                        </button>
                    </td>
                    <td class="px-8 py-5 text-xs font-black text-zinc-900 not-italic">
                        {{ $driver->created_at->format('d M Y') }}
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button class="text-zinc-300 hover:text-black transition-colors p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <p class="text-zinc-400 font-black uppercase text-xs tracking-widest">Aucun livreur trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($drivers->hasPages())
        <div class="px-8 py-6 border-t border-zinc-100 bg-zinc-50/30">
            {{ $drivers->links() }}
        </div>
        @endif
    </div>

    {{-- MODAL AJOUT --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform transition-all border border-zinc-200">
            <div class="p-10">
                <h3 class="text-2xl font-black text-black uppercase tracking-tighter mb-2">Ajouter un Livreur</h3>
                <p class="text-sm text-zinc-500 mb-8 font-medium">Configurez l'accès pour votre nouveau collaborateur.</p>
                
                <form wire:submit.prevent="saveDriver" class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Nom Complet</label>
                        <input wire:model="name" type="text" class="w-full rounded-2xl border-zinc-200 focus:ring-black focus:border-black text-sm font-bold p-4 bg-zinc-50 border-none shadow-inner">
                        @error('name') <span class="text-red-500 text-[10px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-black mb-2">Adresse Email</label>
                        <input wire:model="email" type="email" class="w-full rounded-2xl border-zinc-200 focus:ring-black focus:border-black text-sm font-bold p-4 bg-zinc-50 border-none shadow-inner">
                        @error('email') <span class="text-red-500 text-[10px] font-black uppercase mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-zinc-50 p-4 rounded-2xl border border-dashed border-zinc-200 mb-6">
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-wide">Note de sécurité</p>
                        <p class="text-xs text-black font-black mt-1">Mot de passe par défaut : <span class="text-blue-600">passer123</span></p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="button" wire:click="$set('showModal', false)" 
                            class="flex-1 px-6 py-4 rounded-2xl border border-zinc-200 text-xs font-black uppercase text-black hover:bg-zinc-50 transition">
                            Annuler
                        </button>
                        <button type="submit" 
                            class="flex-1 px-6 py-4 rounded-2xl bg-black text-white text-xs font-black uppercase hover:bg-zinc-800 transition shadow-xl shadow-zinc-200">
                            Créer le compte
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>