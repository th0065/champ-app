<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation - ARAME</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style> #map { height: 200px; border-radius: 1rem; margin-top: 10px; } </style>
</head>
<body class="bg-gray-50 p-4">
    <div class="max-w-xl mx-auto py-6">
        <h1 class="text-2xl font-bold mb-6">Finaliser ma commande 📦</h1>

        <form action="{{ route('order.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="bg-white p-6 rounded-3xl shadow-sm mb-4 border border-gray-100">
                <h2 class="font-bold mb-4">1. Lieu de livraison</h2>
                
                <button type="button" onclick="getLocation()" class="w-full bg-blue-50 text-blue-600 font-bold py-4 rounded-2xl border-2 border-dashed border-blue-200 mb-4 hover:bg-blue-100 transition">
                    📍 Utiliser ma position GPS actuelle
                </button>

                <div id="manual-zone" class="mb-4">
                    <label class="text-xs text-gray-400 uppercase font-bold px-1">Ou choisir un quartier</label>
                    <select name="delivery_point" class="w-full bg-gray-50 border-0 rounded-xl p-4 mt-1">
                        <option value="">-- Sélectionner --</option>
                        <option value="Plateau">Dakar Plateau</option>
                        <option value="Medina">Médina</option>
                        <option value="Ouakam">Ouakam</option>
                    </select>
                </div>

                <div id="map-container" class="hidden">
                    <div id="map"></div>
                    <p class="text-[10px] text-gray-400 mt-2">Vous pouvez déplacer le curseur pour plus de précision.</p>
                </div>

                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">

                <textarea name="address" required placeholder="Ex: Porte bleue, à côté de la boutique..." class="w-full bg-gray-50 border-0 rounded-xl p-4 mt-4 focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm mb-6 border border-gray-100">
                <h2 class="font-bold mb-4">2. Mode de paiement</h2>
                <div class="grid grid-cols-2 gap-3">
                    <label class="border p-4 rounded-2xl flex flex-col items-center cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="payment_method" value="cash" class="hidden" checked onchange="togglePayDetails(false)">
                        <span class="text-lg">💵</span>
                        <span class="text-xs font-bold">Espèces</span>
                    </label>
                    <label class="border p-4 rounded-2xl flex flex-col items-center cursor-pointer has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="payment_method" value="mobile_money" class="hidden" onchange="togglePayDetails(true)">
                        <span class="text-lg">📱</span>
                        <span class="text-xs font-bold">Mobile Money</span>
                    </label>
                </div>
                <div id="pay-info" class="hidden mt-4 p-4 bg-orange-50 rounded-2xl text-[11px] text-orange-700">
                    Paiement sécurisé via PayTech (Wave / Orange Money) avec votre numéro : <strong>{{ Auth::user()->phone }}</strong>
                </div>
            </div>

            <div class="bg-gray-900 text-white p-8 rounded-[2.5rem] shadow-2xl">
                <div class="flex justify-between opacity-60 text-sm mb-2">
                    <span>Produits</span>
                    <span id="prod-price">{{ number_format(collect(session('cart'))->sum(fn($i) => $i['price']*$i['quantity']), 0, ',', ' ') }} F</span>
                </div>
                <div class="flex justify-between opacity-60 text-sm mb-4">
                    <span>Livraison (GPS)</span>
                    <span id="delivery-price">0 F</span>
                </div>
                <div class="flex justify-between text-2xl font-black border-t border-white/10 pt-4">
                    <span>Total</span>
                    <span id="final-price">---</span>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white font-bold py-5 rounded-2xl mt-8 hover:bg-green-400 transition uppercase tracking-widest">
                    Confirmer la commande
                </button>
            </div>
        </form>
    </div>

    <script>
        const shopPos = [14.6677, -17.4358]; // Boutique Dakar
        const totalW = {{ collect(session('cart'))->sum(fn($i) => $i['weight']*$i['quantity']) }};
        const prodP = {{ collect(session('cart'))->sum(fn($i) => $i['price']*$i['quantity']) }};
        var map = L.map('map').setView(shopPos, 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker;

        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const {latitude, longitude} = pos.coords;
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                    document.getElementById('manual-zone').classList.add('hidden');
                    document.getElementById('map-container').classList.remove('hidden');

                    if (marker) map.removeLayer(marker);
                    marker = L.marker([latitude, longitude], {draggable: true}).addTo(map);
                    map.setView([latitude, longitude], 15);
                    setTimeout(() => map.invalidateSize(), 400);

                    // Calcul de la distance
                    const dist = map.distance(shopPos, [latitude, longitude]) / 1000;
                    const fee = Math.max(500, Math.round(dist * 500 * Math.ceil(totalW / 10)));
                    
                    document.getElementById('delivery-price').innerText = fee + " F";
                    document.getElementById('final-price').innerText = (prodP + fee) + " F";
                });
            }
        }

        function togglePayDetails(show) {
            document.getElementById('pay-info').classList.toggle('hidden', !show);
        }
    </script>
</body>
</html>