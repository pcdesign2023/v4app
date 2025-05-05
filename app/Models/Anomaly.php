<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anomaly extends Model
{
    use HasFactory;

    protected $table = 'anomalies'; // Ensure the correct table name

    protected $primaryKey = 'AnoID'; // Explicitly set the primary key


    protected $fillable = ['Libele'];

    public $timestamps = true; // Ensure timestamps are handled correctly
}
