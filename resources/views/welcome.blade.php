<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARAME - Produits Frais</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 antialiased">
   <nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between">
        <a href="/" class="text-2xl font-black text-green-600 tracking-tighter italic">ARAME.</a>

        <div class="flex items-center gap-3 md:gap-6">
            <a href="{{ route('cart.index') }}" class="relative p-3 bg-gray-100 rounded-2xl hover:bg-gray-200 transition">
                <span class="text-xl">🛒</span>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full border-2 border-white">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>

            @auth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 p-1.5 pr-4 bg-gray-100 rounded-2xl hover:bg-gray-200 transition">
                    <div class="w-10 h-10 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="hidden md:block">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Mon compte</p>
                        <p class="text-sm font-bold text-gray-900 leading-none">{{ Auth::user()->name }}</p>
                    </div>
                </a>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="hidden md:block text-sm font-bold text-gray-500 hover:text-green-600 px-4">
                        Connexion
                    </a>
                    <a href="{{ route('register') }}" class="bg-green-600 text-white px-5 py-3 rounded-2xl font-bold text-sm hover:bg-green-700 shadow-lg shadow-green-100 transition">
                        S'inscrire
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Nos Fruits & Légumes 🍎</h1>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-3xl p-3 shadow-sm border border-gray-100 flex flex-col">
                <img src="{{ $product->image_url }}" class="w-full h-32 md:h-48 object-cover rounded-2xl mb-4">
                <div class="px-2 flex-grow">
                    <h3 class="font-bold text-gray-800 text-sm md:text-base mb-1">{{ $product->name }}</h3>
                    <p class="text-xs text-gray-400 mb-2">{{ $product->weight }} {{ $product->unit }}</p>
                    
                    <div class="flex items-center justify-between mt-auto">
                        <span class="font-black text-green-600 text-lg">{{ number_format($product->price, 0, ',', ' ') }} F</span>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white p-2.5 rounded-xl transition shadow-lg shadow-orange-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $products->links() }}
        </div>
    </main>
</body>
</html>