<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis massivement.
     */
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
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}