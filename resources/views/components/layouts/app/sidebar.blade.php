<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    {{-- Bouton pour fermer la sidebar sur mobile --}}
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

  
  @if(auth()->user()->role === 'admin')
    {{-- Titre de l'application --}}
    <div class="px-6 py-4">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">ARAME Admin</h2>
    </div>
    {{-- Liste de Navigation --}}
    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            Accueil
        </flux:navlist.item>

        <flux:navlist.item icon="shopping-cart" :href="route('orders.index')" :current="request()->routeIs('orders.index')" wire:navigate>
         Commandes
         </flux:navlist.item>

        <flux:navlist.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.index')" wire:navigate>
            Produits
        </flux:navlist.item>

        <flux:navlist.item icon="archive-box" href="#" wire:navigate>
            Stocks
        </flux:navlist.item>

        <flux:navlist.item icon="truck" :href="route('delivery.index')" :current="request()->routeIs('delivery.index')" wire:navigate>
            Livraisons
        </flux:navlist.item>

        <flux:navlist.item icon="map" href="#" wire:navigate>
            Zones
        </flux:navlist.item>
    </flux:navlist>
      @elseif(auth()->user()->role === 'driver')
        {{-- Titre de l'application --}}
    <div class="px-6 py-4">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">ARAME Shop</h2>
    </div>
    {{-- Liste de Navigation pour Driver --}}
    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            Accueil
        </flux:navlist.item>    

    <flux:spacer />
    </flux:navlist>
     @elseif(auth()->user()->role === 'buyer')
    <div class="px-6 py-4">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">ARAME Shop</h2>
    </div>

    <flux:navlist variant="outline">
        <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            Mon Tableau de bord
        </flux:navlist.item>

       

       

        <flux:spacer />

        {{-- Un item pour le support en bas --}}
        <flux:navlist.item icon="question-mark-circle" href="#" wire:navigate>
            Aide & Support
        </flux:navlist.item>
    </flux:navlist>
@endif



    {{-- Menu Utilisateur (Profil + Déconnexion) --}}
    <div class="p-4 border-t border-zinc-200 dark:border-zinc-700 mt-auto">
        <flux:dropdown position="top" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down"
                class="cursor-pointer"
            />

            <flux:menu class="w-[220px]">
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    Paramètres
                </flux:menu.item>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        Déconnexion
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </div>
</flux:sidebar>