<?php

namespace App\Imports;

use App\Models\FabOrder;
use App\Models\Product;
use App\Models\Client;
use App\Models\Chaine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class FabOrdersImport implements ToCollection, WithHeadingRow
    {
        public function collection(Collection $rows)
        {
            foreach ($rows as $row) {
                // Normalize keys (lowercase and underscores)
                $row = collect($row)->mapWithKeys(function ($value, $key) {
                    return [strtolower(trim(str_replace(['*', ' '], ['','_'], $key))) => $value];
                });

                // Validate required fields
                $validator = Validator::make($row->toArray(), [
                    'of' => 'required|unique:fab_orders,OFID',
                    'reference' => 'required',
                    'chaine' => 'required',
                    'commande' => 'required',
                    'client' => 'required',
                ]);

                // Debug validation failures
                if ($validator->fails()) {
                    Log::warning('Validation failed', $validator->errors()->all());
                    continue;
                }

                // Lookup related models
                $product = Product::where('ref_id', $row['reference'])->first();
                $client = Client::where('name', $row['client'])->first();
                $chaine = Chaine::where('Num_chaine', (string) $row['chaine'])->first();

                // Debug missing relations
                if (!$product || !$client || !$chaine) {
                    Log::warning('Missing related model:', [
                        'product' => $product ? 'found' : 'missing',
                        'client' => $client ? 'found' : 'missing',
                        'chaine' => $chaine ? 'found' : 'missing',
                        'row' => $row->toArray(),
                    ]);
                    continue;
                }

                // Convert Excel date
                $dateFabrication = null;
                if (!empty($row['date_planif'])) {
                    try {
                        // Try to parse as Excel numeric date first
                        if (is_numeric($row['date_planif'])) {
                            $carbonDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(
                                floatval($row['date_planif'])
                            );
                            $dateFabrication = \Carbon\Carbon::instance($carbonDate)->format('Y-m-d H:i:s');
                        }
                        // Otherwise parse as string date
                        else {
                            $dateFabrication = \Carbon\Carbon::createFromFormat('d/m/Y', $row['date_planif'])
                                ->format('Y-m-d H:i:s');
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Date conversion failed', [
                            'value' => $row['date_planif'],
                            'error' => $e->getMessage()
                        ]);
                        $datePlanif = null;
                    }
                }

                // Debug the row before saving
                Log::info('Inserting FabOrder', $row->toArray());
                Log::info('Converted date:', ['raw' => $row['date_planif'], 'converted' => $dateFabrication]);

                // Create the record
                FabOrder::create([
                    'OFID' => $row['of'],
                    'Prod_ID' => $product->id,
                    'prod_ref' => $row['reference'],              // <-- Set from Excel column
                    'prod_name' => $product->product_name,                // <-- Set from Product model
                    'chaineID' => $chaine->id,
                    'client_id' => $client->id,
                    'saleOrderId' => $row['commande'],
                    'Lot_Set' => $row['lot'] ?? null,
                    'Pf_Qty' => $row['pf_qte'] ?? 0,
                    'Set_qty' =>$row['set_qte'] ?? 0,
                    'Tester_qty' =>$row['tes_qte'] ?? 0,
                    'Sf_Qty' => $row['sf_qty'] ?? 0,
                    'instruction' => $client->instruction ?? null,
                    'comment' => $row['commentaire'] ?? null,
                    'date_fabrication' => $dateFabrication,
                ]);
            }
        }
    }
