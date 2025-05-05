<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    use HasFactory;

    protected $table = 'quality';

    protected $fillable = [
        'chaineID', 'OF_ID' // ✅ Removed unnecessary columns
    ];

    public function chaine()
    {
        return $this->belongsTo(Chaine::class, 'chaineID','id');
    }

    public function fabOrder()
    {
        return $this->belongsTo(FabOrder::class, 'OF_ID','id');
    }

    public function anomaly()
    {
        return $this->belongsTo(Anomaly::class, 'AnoID', 'AnoID'); // ✅ Match foreign key correctly
    }
    /**
     * Relationship: One Quality has many Conformity Details
     */
    public function conformityDetails()
    {
        return $this->hasMany(ConformityDetail::class, 'Qlty_id');
    }
}
