<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chaine extends Model
{
    protected $primaryKey = 'id'; // Update the primary key if necessary

    use HasFactory;
    protected $table = 'chaine'; // Explicitly specify the table name
    protected $fillable = [
        'Num_chaine',
        'responsable_QLTY_id',
        'chef_de_chaine_id',
        'nbr_operateur',
    ];

    public function responsableQLTY()
    {
        return $this->belongsTo(User::class, 'responsable_QLTY_id');
    }

    public function chefDeChaine()
    {
        return $this->belongsTo(User::class, 'chef_de_chaine_id', 'id'); // ✅ Links Chaine to its Chef de Chaine
    }

    public function fabOrders()
    {
        return $this->hasMany(FabOrder::class, 'chaineID', 'id'); // ✅ Ensures correct foreign key
    }
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_de_chaine_id'); // Ensure the foreign key is correct
    }

}
