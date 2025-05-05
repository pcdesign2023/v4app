<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Planning;

class PlanningController extends Controller
{
    public function getSaleOrders(Request $request)
    {
        $client_id = $request->input('client_id');

        $saleOrders = \App\Models\Planning::where('Client_id', $client_id)
            ->select('id', 'N_commande as sale_order_id') // make sure N_commande has values
            ->distinct()
            ->get();

        return response()->json($saleOrders);
    }


    public function addplanning()
    {
        $clients = Client::all();
        $plannings = Planning::with('client')->latest()->get();

        return view('planning.add', compact('clients', 'plannings'));
    }
    public function index()
    {
        $clients = Client::all();
        $plannings = Planning::with('client')->latest()->get();

        return view('planning.index', compact('clients', 'plannings'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'N_commande'    => 'required|string',
            'Client_id'     => 'required|exists:clients,id',
            'date_Planif'   => 'nullable|date',
            'date_debut'    => 'nullable|date',
            'date_fin'      => 'nullable|date',
            'Instruction'   => 'nullable|string',
        ]);

        $planning = Planning::findOrFail($id);
        $planning->update($request->all());

        return redirect()->back()->with('success', 'Planning mis à jour avec succès.');
    }

    public function destroy($id)
    {
        Planning::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Planning supprimé.');
    }
    // Store a new planning record
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'N_commande'    => 'required|string|max:255',
            'Client_id'     => 'required|exists:clients,id',
            'date_Planif'   => 'nullable|date',
            'date_debut'    => 'nullable|date',
            'date_fin'      => 'nullable|date',
            'Instruction'   => 'nullable|string|max:1000',
        ]);

        // Create planning
        Planning::create($validated);

        return redirect()->back()->with('success', 'Planning enregistré avec succès.');
    }
}
