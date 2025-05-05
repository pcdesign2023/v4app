<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ofplanning extends Model
{
    use HasFactory;

    protected $table = 'ofplanning';

    protected $fillable = [
        'OFID',
        'prod_ref',
        'prod_des',
        'client',
        'date_planifie',
        'commande',
        'qte_plan',
        'Priority',
        'qty_produced',
        'qte_reel',
        'statut',
        'instruction',
        'comment',
    ];
}
