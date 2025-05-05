<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Planning extends Model
{
    use HasFactory;

    protected $table = 'planning'; // Optional if your table is named correctly

    protected $fillable = [
        'N_commande',
        'Client_id',
        'date_Planif',
        'date_debut',
        'date_fin',
        'Instruction',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class, 'Client_id');
    }
}