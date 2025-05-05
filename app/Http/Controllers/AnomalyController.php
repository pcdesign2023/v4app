<?php
namespace App\Http\Controllers;

use App\Models\Anomaly;
use Illuminate\Http\Request;
use App\Models\DefaultEntry;
class AnomalyController extends Controller
{
    public function index()
    {
        $anomalies = Anomaly::all();
        $default_entries = DefaultEntry::with('anomaly')->get();

        return view('anomalies.index', compact('anomalies', 'default_entries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Libele' => 'required|unique:anomalies,Libele',
        ]);

        Anomaly::create($request->all());

        return redirect()->route('anomalies.index')->with('success', 'Anomaly created successfully.');
    }

    public function update(Request $request, Anomaly $anomaly)
    {
        $request->validate([
            'Libele' => 'required|unique:anomalies,Libele,' . $anomaly->AnoID . ',AnoID', // Specify AnoID as the primary key
        ]);

        $anomaly = Anomaly::findOrFail($anomaly->AnoID); // Find the anomaly by AnoID
        $anomaly->update($request->all());

        return redirect()->route('anomalies.index')->with('success', 'Anomaly updated successfully.');

    }

    public function destroy(Anomaly $anomaly)
    {
        $anomaly->delete();
        return redirect()->route('anomalies.index')->with('success', 'Anomaly deleted successfully.');
    }
}
