<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anomaly;

class AnomaliesTableSeeder extends Seeder
{
    public function run()
    {
        $anomalies = [
            ['AnoID' => 1, 'Libele' => 'Flacon'],
            ['AnoID' => 2, 'Libele' => 'Pompe'],
            ['AnoID' => 4, 'Libele' => 'C.Pompe'],
            ['AnoID' => 5, 'Libele' => 'Capot'],
            ['AnoID' => 6, 'Libele' => 'Etiquette'],
            ['AnoID' => 7, 'Libele' => 'Cale'],
            ['AnoID' => 8, 'Libele' => 'Etui'],
            ['AnoID' => 9, 'Libele' => 'Jus'],
        ];

        foreach ($anomalies as $anomaly) {
            Anomaly::firstOrCreate(['AnoID' => $anomaly['AnoID']], $anomaly);
        }
    }
}
