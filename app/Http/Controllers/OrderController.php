<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation de base
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'payment_method' => 'required'
        ]);

        $cart = session('cart');
        
        // 2. Calcul du poids total et prix des produits
        $totalWeight = 0;
        $totalProductsPrice = 0;
        foreach($cart as $item) {
            $totalWeight += $item['weight'] * $item['quantity'];
            $totalProductsPrice += $item['price'] * $item['quantity'];
        }

       $shopLat = 14.6677; 
    $shopLng = -17.4358;
    $distance = $this->calculateDistance($shopLat, $shopLng, $request->latitude, $request->longitude);

    // 4. Calcul des frais par paliers de 5 (500 F par tranche de 5km ou 5kg)
    $distanceTier = ceil($distance / 2);      // Ex: 7km / 5 = 1.4 -> 2 tranches
    $weightTier = ceil($totalWeight / 2);    // Ex: 2kg / 5 = 0.4 -> 1 tranche

    // On prend la tranche la plus haute pour déterminer le prix
    $maxTier = max($distanceTier, $weightTier, 1); // Minimum 1 tranche (500 F)

    $deliveryFee = $maxTier * 500;
            
        // Sécurité : Frais minimum de 500 F
        if($deliveryFee < 500) $deliveryFee = 500;

        // 5. Création de la commande
        $order = Order::create([
            'user_id' => auth()->id(),
            'payment_method' => $request->payment_method,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'total_weight' => $totalWeight,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $totalProductsPrice + $deliveryFee,
            'status' => 'pending'
        ]);

        // 6. Gestion du paiement Mobile Money (Exemple PayTech)
        if ($request->payment_method === 'mobile_money') {
            return $this->handlePayTechPayment($order);
        }

        // 7. Si Cash, on vide le panier et on confirme
        session()->forget('cart');
        return redirect()->route('order.success', $order->id);
    }

    public function success($id)
    {
        $order = Order::findOrFail($id);
        return view('order-success', compact('order'));
    }

    // Algorithme de calcul de distance GPS (Haversine)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $km = $dist * 60 * 1.1515 * 1.609344;
        return $km;
    }

    private function handlePayTechPayment($order) {
        // Logique PayTech à insérer ici (Redirection vers l'API)
        // Pour l'instant, on simule une réussite
        session()->forget('cart');
        return redirect()->route('order.success', $order->id)->with('success', 'Paiement en ligne initialisé.');
    }
}