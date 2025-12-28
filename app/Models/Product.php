<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'weight', // Poids en kg (ex: 1.5 pour 1kg500)
        'unit',   // Unité d'affichage (ex: "kg", "sac", "pièce")
        'stock',
        'image_url',
    ];
}