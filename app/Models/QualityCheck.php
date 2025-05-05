<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityCheck extends Model
{
    protected $fillable = [
        'fabrication_order_id',
        'quantity_conform',
        'quantity_nonconform',
        'checked_by_user_id',
    ];

    public function defects()
    {
        return $this->hasMany(QualityDefect::class);
    }

    public function fabricationOrder()
    {
        return $this->belongsTo(FabricationOrder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'checked_by_user_id');
    }
}
