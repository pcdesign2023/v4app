<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConformityDetail extends Model
{
    use HasFactory;

    protected $table = 'conformity_details';

    /**
     * Allow mass assignment for these fields.
     */
    protected $fillable = [
        'OFID',
        'AnoId',
        'fk_OFID',
        'type_product',
        'Qty_NC',
        'Default',
        'RespDefaut',
        'DateInterv',
        'Comment',
        'Component',
    ];

    /**
     * Laravel 11+ casts() method (NON-STATIC)
     */
    public function casts(): array
    {
        return [
            'DateInterv' => 'datetime:Y-m-d H:i', // If this column exists
            'Qty_NC'     => 'integer',
        ];
    }

    /**
     * Relationship: A conformity detail belongs to an anomaly.
     */
    public function anomaly()
    {
        return $this->belongsTo(Anomaly::class, 'AnoId', 'AnoID');
    }
    public function fabrication()
    {
        return $this->belongsTo(FabOrder::class, 'fk_OFID', 'ID');
    }
}
