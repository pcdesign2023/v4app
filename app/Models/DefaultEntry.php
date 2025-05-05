<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

    class DefaultEntry extends Model
    {
        use HasFactory;

        protected $table = 'default_entries';

        public $incrementing = false; // ✅ Disable auto-increment
        protected $primaryKey = 'id'; // ✅ Explicitly set primary key

        protected $fillable = [
            'id', // ✅ Allow manual input of id
            'AnoID',
            'label'
        ];
        public function anomaly()
        {
            return $this->belongsTo(Anomaly::class, 'AnoID');
        }
    }
