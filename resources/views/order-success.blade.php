<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci ! - ARAME</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-600 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-[3rem] p-10 max-w-md w-full text-center shadow-2xl">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            ✅
        </div>
        <h1 class="text-3xl font-black text-gray-900 mb-2">C'est validé !</h1>
        <p class="text-gray-500 mb-8">Votre commande <strong>#{{ $order->id }}</strong> est en cours de préparation.</p>
        
        <div class="bg-gray-50 rounded-3xl p-6 text-left space-y-3 mb-8">
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Livraison :</span>
                <span class="font-bold">{{ number_format($order->delivery_fee, 0, ',', ' ') }} F</span>
            </div>
            <div class="flex justify-between text-lg pt-3 border-t">
                <span class="font-bold">Total payé :</span>
                <span class="font-black text-green-600">{{ number_format($order->total_amount, 0, ',', ' ') }} F</span>
            </div>
        </div>

        <p class="text-xs text-gray-400 mb-8">Un livreur vous contactera sur votre numéro : <br><strong>{{ $order->user->phone }}</strong></p>

        <a href="/" class="block w-full bg-gray-900 text-white font-bold py-4 rounded-2xl hover:bg-gray-800 transition">
            Retour à l'accueil
        </a>
    </div>
</body>
</html>