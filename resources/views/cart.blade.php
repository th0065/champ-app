<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - ARAME</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-4 md:p-10">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="/" class="p-2 bg-white rounded-full shadow-sm">⬅️</a>
            <h1 class="text-3xl font-bold">Mon Panier 🛒</h1>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                <div class="p-6 md:p-8 space-y-6">
                    @php $totalPrice = 0; $totalWeight = 0; @endphp
                    @foreach(session('cart') as $id => $details)
                        @php 
                            $totalPrice += $details['price'] * $details['quantity'];
                            $totalWeight += $details['weight'] * $details['quantity'];
                        @endphp
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $details['image'] }}" class="w-16 h-16 rounded-2xl object-cover">
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $details['name'] }}</h4>
                                    <p class="text-xs text-gray-400">{{ $details['weight'] }}kg x {{ $details['quantity'] }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="flex items-center bg-gray-100 rounded-xl p-1">
                                    <form action="{{ route('cart.update', $id) }}" method="POST">
                                        @csrf
                                        <button name="action" value="decrease" class="w-8 h-8 flex items-center justify-center font-bold">-</button>
                                        <span class="px-2 font-bold">{{ $details['quantity'] }}</span>
                                        <button name="action" value="increase" class="w-8 h-8 flex items-center justify-center font-bold">+</button>
                                    </form>
                                </div>
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    <button class="text-red-400">🗑️</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 p-8 border-t border-dashed">
                    <div class="flex justify-between text-gray-500 mb-2">
                        <span>Poids total :</span>
                        <span class="font-bold">{{ $totalWeight }} kg</span>
                    </div>
                    <div class="flex justify-between text-2xl font-black text-gray-900">
                        <span>Sous-total :</span>
                        <span>{{ number_format($totalPrice, 0, ',', ' ') }} F</span>
                    </div>
                    <a href="{{ route('cart.checkout') }}" class="block w-full text-center bg-green-600 text-white font-bold py-5 rounded-2xl mt-8 shadow-lg shadow-green-100 hover:scale-[1.02] transition">
                        Passer à la livraison
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-20">
                <p class="text-gray-400 mb-4">Votre panier est vide...</p>
                <a href="/" class="text-green-600 font-bold underline">Retourner faire les courses</a>
            </div>
        @endif
    </div>
</body>
</html>