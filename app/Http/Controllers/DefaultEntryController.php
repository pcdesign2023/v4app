<?php

namespace App\Http\Controllers;

use App\Models\DefaultEntry;
use App\Models\Anomaly;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DefaultEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $anomalies = Anomaly::all();
        $default_entries = DefaultEntry::with('anomaly')->get();

        return view('anomalies.index', compact('anomalies', 'default_entries'));
    }

    public function getByAnomaly($AnoID)
    {
        $defaultEntries = DefaultEntry::where('AnoID', $AnoID)->get();
        return response()->json($defaultEntries);
    }
    public function create($Anoid)
    {
        $anomalies = Anomaly::all(); // Fetch anomalies

        return view('default_entries.create', compact('Anoid', 'anomalies'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $Anoid)
    {
        // Validate input
        $request->validate([
            'id' => 'required|integer|unique:default_entries,id',
            'AnoID' => 'required|exists:anomalies,AnoID', // ✅ Fix foreign key validation
            'label' => 'required|string|max:255',
        ]);

        // Ensure the AnoID from the URL matches the request
        if ($Anoid != $request->input('AnoID')) {
            return redirect()->back()->withErrors(['AnoID' => 'Mismatch in AnoID'])->withInput();
        }

        // Create a new entry
        DefaultEntry::create([
            'id' => $request->id,
            'AnoID' => $Anoid, // ✅ Use URL parameter
            'label' => $request->label,
        ]);

        return redirect()->back()->with('success', 'Default entry created successfully.');
    }




    /**
     * Display the specified resource.
     */
    public function show(DefaultEntry $defaultEntry)
    {
        return response()->json($defaultEntry);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $originalId)
    {
        // Validate input
        $request->validate([
            'id' => [
                'required',
                'integer',
                // Only enforce uniqueness if ID is changing
                function ($attribute, $value, $fail) use ($originalId) {
                    if ($value != $originalId && DefaultEntry::where('id', $value)->exists()) {
                        $fail('The id has already been taken.');
                    }
                },
            ],
            'label' => 'required|string|max:255',
        ]);

        // Find the existing entry
        $defaultEntry = DefaultEntry::where('id', $originalId)->first();

        if (!$defaultEntry) {
            return response()->json(['message' => 'Default entry not found'], 404);
        }

        // Update the entry with a possible ID change using a transaction
        DB::transaction(function () use ($defaultEntry, $request) {
            if ($defaultEntry->id != $request->id) {
                // Create a new record with the new ID
                DefaultEntry::create([
                    'id' => $request->id,
                    'AnoID' => $defaultEntry->AnoID,
                    'label' => $request->label,
                ]);
                // Delete old record
                $defaultEntry->delete();
            } else {
                // Update the label if ID remains the same
                $defaultEntry->update([
                    'label' => $request->label,
                ]);
            }
        });

        return response()->json(['message' => 'Default entry updated successfully']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DefaultEntry $defaultEntry)
    {
        $defaultEntry->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }

}

