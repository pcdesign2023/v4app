<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FabOrder extends Model
{
    use HasFactory;

    protected $table = 'fab_orders';

    protected $fillable = [
        'OFID', 'Prod_ID', 'chaineID', 'saleOrderId', 'creation_date_Of',
        'client_id', 'Pf_Qty', 'Sf_Qty', 'Set_qty', 'Tester_qty', 'Lot_Set',
        'instruction', 'Comment_chaine','date_fabrication',
         'End_Prod', 'Statut_of','comment',

    ];
    protected function casts(): array
    {
        return [
            'date_fabrication' => 'datetime:Y-m-d', // Ensures only date is used
        ];
    }

    public $timestamps = false;
    public static function boot()
    {
        parent::boot();

        static::creating(function ($fabOrder) {
            // Set default values if empty
            $fabOrder->Lot_Set = $fabOrder->Lot_Set ?? '';
            $fabOrder->creation_date_Of = Carbon::now(); // Set the current date & time
            $fabOrder->Sf_Qty = is_numeric($fabOrder->Sf_Qty) ? $fabOrder->Sf_Qty : 0;
            $fabOrder->Set_qty = is_numeric($fabOrder->Set_qty) ? $fabOrder->Set_qty : 0;
            $fabOrder->Tester_qty = is_numeric($fabOrder->Tester_qty) ? $fabOrder->Tester_qty : 0;
        });
    }
    /**
     * Generate the next `OFID` in the format `W/F/0001`.
     */


    public function product()
    {
        return $this->belongsTo(Product::class, 'Prod_ID');
    }

    public function chaine()
    {
        return $this->belongsTo(Chaine::class, 'chaineID', 'id');
    }
    public function quality()
    {
        return $this->hasOne(Quality::class, 'OF_ID', 'id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function conformityDetails()
    {
        return $this->hasMany(ConformityDetail::class, 'fk_OFID', 'ID');
    }
}
