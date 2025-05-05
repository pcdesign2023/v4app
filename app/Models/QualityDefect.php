<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityDefect extends Model
{
    protected $fillable = [
        'quality_check_id',
        'defect_type',
        'product_component_id',
        'quantity'
    ];

    public function qualityCheck()
    {
        return $this->belongsTo(QualityCheck::class);
    }

    public function productComponent()
    {
        return $this->belongsTo(Product::class, 'product_component_id');
    }
}
