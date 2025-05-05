<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImportController extends Controller
{
    public function showImportForm()
    {
        return view('data_import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file');

        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
            $header = fgetcsv($handle, 1000, ","); // Read CSV headers

            $csvHeaders = ["Product", "Product Code", "Component", "Component Code", "Quantity", "UOM"];
            if ($header !== $csvHeaders) {
                return redirect()->back()->with('error', 'Invalid CSV format! Please use the correct template.');
            }

            $importedCount = 0;
            $skippedCount = 0;
            $insertData = [];
            $batchSize = 500; // ✅ Limit batch size to prevent MySQL error

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $productName = trim($row[0]);
                $refId = trim($row[1]);
                $componentName = trim($row[2]);
                $componentCode = trim($row[3]);
                $quantity = is_numeric($row[4]) ? floatval($row[4]) : 0;

                // ✅ Skip rows with empty required fields
                if (!$productName || !$refId || !$componentName || !$componentCode) {
                    $skippedCount++;
                    continue;
                }

                // ✅ Add row to insert batch
                $insertData[] = [
                    'product_name' => $productName,
                    'ref_id' => $refId,
                    'component_name' => $componentName,
                    'component_code' => $componentCode,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // ✅ Insert when batch reaches the limit
                if (count($insertData) >= $batchSize) {
                    Product::upsert($insertData, ['product_name', 'ref_id', 'component_name', 'component_code'], ['quantity']);
                    $importedCount += count($insertData);
                    $insertData = []; // Reset batch
                }
            }

            fclose($handle);

            // ✅ Insert remaining records
            if (!empty($insertData)) {
                Product::upsert($insertData, ['product_name', 'ref_id', 'component_name', 'component_code'], ['quantity']);
                $importedCount += count($insertData);
            }

            // ✅ Build notification message
            $message = "Imported: {$importedCount} new products.";
            if ($skippedCount > 0) {
                $message .= " Skipped: {$skippedCount} invalid rows.";
            }

            return redirect()->route('data_import.index')->with('success', $message);
        }

        return redirect()->back()->with('error', 'Error processing the file. Please try again.');
    }



}
