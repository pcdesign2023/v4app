<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabrication extends Model
{
    use HasFactory;

    protected $table = 'fabrication'; // Table name

    protected $fillable = [
        'OFID',
        'Lot_Jus',
        'Valid_date',
        'effectif_Reel',
        'date_fabrication',
        'Pf_Qty',
        'Sf_Qty',
        'Set_qty',
        'Tester_qty',
        'End_Fab_date',
        'Comment_chaine'
    ];
    protected function casts(): array
    {
        return [
            'Valid_date' => 'datetime:Y-m-d', // Ensures only date is used
        ];
    }

    // ✅ Relationship with FabOrders (One Fabrication belongs to one FabOrder)
    public function fabOrder()
    {
        return $this->belongsTo(FabOrder::class, 'OFID', 'OFID');
    }
}
