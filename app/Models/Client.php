<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'instruction'
    ];

    public function fabOrders()
    {
        return $this->hasMany(FabOrder::class, 'client_id');
    }
}
