<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method',
        'latitude',
        'longitude',
        'address',
        'total_weight',
        'delivery_fee',
        'total_amount',
        'status',
    ];

    /**
     * Relation avec l'utilisateur (Client)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION À AJOUTER : Une commande contient plusieurs articles
     */
    public function items(): HasMany
    {
        // On suppose que votre modèle s'appelle OrderItem
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relation avec la livraison
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    
}