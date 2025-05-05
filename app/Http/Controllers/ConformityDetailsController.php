<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quality;
use App\Models\DefaultEntry;
use App\Models\ConformityDetail;
use App\Models\Anomaly;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ConformityDetailsController extends Controller
{
    /**
     * Store multiple conformity details for a Quality record.
     */
    public function store(Request $request)
    {

        // Validate the incoming request data
        $validatedData = $request->validate([
            'OFID' => 'required|string',
            'AnoId' => 'required|integer',
            'fk_OFID' => 'required|integer',
            'type_product' => 'required|string',
            'Qty_NC' => 'required|integer',
            'Default' => 'nullable', // Allow any type initially
            'RespDefaut' => 'nullable|string',
            'DateInterv' => 'nullable|date',
            'Component' => 'required|string',
            'Comment' => 'nullable|string',
        ]);

        // Handle the 'Default' field
        $defaultValue = $validatedData['Default'];

        // Convert the Default value to an integer if possible
        if ($defaultValue) {
            if (is_string($defaultValue)) {
                // If the value is a string, attempt to convert it to an integer
                $defaultValue = filter_var($defaultValue, FILTER_VALIDATE_INT);

                if ($defaultValue === false || $defaultValue === null) {
                    return back()->withErrors(['Default' => 'The Default value must be a valid integer.']);
                }
            } elseif (is_array($defaultValue)) {
                // If the value is an array, extract the first element and convert it to an integer
                $defaultValue = (int) ($defaultValue[0] ?? null);
            } else {
                // If the value is neither a string nor an array, ensure it's an integer
                $defaultValue = (int) $defaultValue;
            }
        } else {
            $defaultValue = null; // Set to null if no value is provided
        }
        // Save the data
        ConformityDetail::create([
            'OFID' => $validatedData['OFID'],
            'AnoId' => $validatedData['AnoId'],
            'fk_OFID' => $validatedData['fk_OFID'],
            'type_product' => $validatedData['type_product'],
            'Qty_NC' => $validatedData['Qty_NC'],
            'Default' => $defaultValue, // Use the transformed value
            'RespDefaut' => $validatedData['RespDefaut'],
            'Component' => $validatedData['Component'],
            'Comment' => $validatedData['Comment'],
        ]);

        return redirect()->back()->with('success', 'Record saved successfully!');
    }
    public function fetchConformityDetails(Request $request)
    {
        // Get the OFID from the query parameter
        $ofid = $request->input('ofid');

        // Fetch records for the given OFID
        $records = DB::table('conformity_details')
            ->leftJoin('default_entries', 'conformity_details.Default', '=', 'default_entries.id')
            ->leftJoin('anomalies', 'conformity_details.AnoId', '=', 'anomalies.AnoID')
            ->leftJoin('products', 'conformity_details.Component', '=', 'products.id')
            ->select(
                'conformity_details.id',
                'conformity_details.OFID',
                'conformity_details.AnoId as anomaly_id',
                'anomalies.Libele as anomaly_label',
                'conformity_details.type_product',
                'conformity_details.Qty_NC',
                'default_entries.id as default_id',
                'default_entries.label as default_label',
                'conformity_details.RespDefaut',
                'conformity_details.Comment',
                'products.id as component_id',
                'products.component_name as Component',
                'products.ref_id as ref_id'
            )
            ->where('conformity_details.OFID', $ofid)
            ->get();

        // Return the records as JSON
        return response()->json($records);
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:conformity_details,id',
            'Component' => 'required|string',
            'Default' => 'required|integer',
            'Qty_NC' => 'required|numeric',
            'type_product' => 'required|string',
            'RespDefaut' => 'required|string',
            'Comment' => 'nullable|string',
        ]);

        $conformity = ConformityDetail::find($request->id);
        $conformity->update($validated);

        return redirect()->back()->with('success', 'Défaut mis à jour avec succès.');
    }
    public function destroy($id)
    {
        $conformity = ConformityDetail::findOrFail($id);
        $conformity->delete();

        return response()->json(['success' => true]);
    }


    // Step 1: Modify Controller to Return Components via AJAX with Validation
// File: ConformityDetailsController.php
    public function getComponents(Request $request)
    {
        try {
            $refId = $request->get('ref_id');

            if (!$refId) {
                return response()->json(['error' => 'Missing ref_id'], 400);
            }

            $components = Product::where('ref_id', (string) $refId)
                ->select('id','component_code', 'component_name')
                ->get();

            if ($components->isEmpty()) {
                return response()->json(['error' => 'No components found'], 404);
            }

            return response()->json($components);
        } catch (\Exception $e) {
            Log::error('Error fetching components: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    /**
     * Load components list based on ref_id.
     */
    public function fetchComponentsByRefId(Request $request)
    {
        $refId = $request->get('ref_id');

        if (!$refId) {
            return response()->json(['error' => 'Missing ref_id'], 400);
        }

        $components = Product::where('ref_id', (string) $refId)
            ->select('id', 'component_code', 'component_name')
            ->get();

        return response()->json($components);
    }

    /**
     * Load all available anomalies.
     */
    public function fetchAnomalies()
    {
        $anomalies = Anomaly::select('AnoID', 'Libele')->get();
        return response()->json($anomalies);
    }

    /**
     * Load default entries by selected anomaly ID.
     */
    public function fetchDefaultEntriesByAnomaly($anomalyId)
    {
        if (!$anomalyId) {
            return response()->json(['error' => 'Missing anomaly ID'], 400);
        }

        $defaults = DefaultEntry::where('AnoID', $anomalyId)
            ->select('id', 'label')
            ->get();

        return response()->json($defaults);
    }
}
