<?php

namespace App\Http\Controllers;
use App\Models\Ofplanning;
use App\Models\Client;
use App\Models\Product;
use App\Models\Planning;

use Illuminate\Http\Request;

class OfplanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Ofplanning::select([
                'id',
                'client',
                'commande',
                'OFID',
                'prod_des',
                'date_planifie',
                'qte_plan',
                'statut',
                'instruction'
            ])->get();

            return response()->json(['data' => $data]);
        }
        $commandes = Planning::select('N_commande')->distinct()->pluck('N_commande');

        $clients = Client::select('name')->distinct()->get();
        $products = Product::select('ref_id', 'product_name')->distinct()->get();

        return view('ofplanning.index', compact('clients', 'products', 'commandes'));
    }

    public function supchainView(Request $request)
    {
        if ($request->ajax()) {
            $data = Ofplanning::select([
                'id', 'client', 'commande', 'OFID',
                'prod_des', 'date_planifie', 'qte_plan',
                'qte_reel', 'statut', 'instruction', 'priority'
            ])->get();

            return response()->json(['data' => $data]);
        }

        $clients = Client::all();
        $products = Product::select('ref_id', 'product_name')->distinct()->get();
        $commandes = Planning::select('N_commande')->distinct()->pluck('N_commande');

        return view('ofplanning.supchain', compact('clients', 'products', 'commandes'));
    }
    public function cerigmpView(Request $request)
    {
        if ($request->ajax()) {
            $data = Ofplanning::select([
                'id', 'client', 'commande', 'OFID',
                'prod_des', 'date_planifie', 'qte_plan',
                'qte_reel', 'statut', 'instruction', 'priority'
            ])->get();

            return response()->json(['data' => $data]);
        }

        $clients = Client::all();
        $products = Product::select('ref_id', 'product_name')->distinct()->get();
        $commandes = Planning::select('N_commande')->distinct()->pluck('N_commande');

        return view('ofplanning.cerigmp', compact('clients', 'products', 'commandes'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'OFID' => 'required|string|max:255',
            'prod_ref' => 'required|string|max:255',
            'prod_des' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'commande' => 'required|string|max:255',
            'qte_plan' => 'required|integer|min:1',
            'qte_reel' => 'nullable|integer|min:0',
            'statut' => 'required|in:Planifié,En cours,Réalisé,Traité',
            'priority'=> 'required|integer|min:0',
            'qty_produced'=> 'nullable|integer|min:0',
            'date_planifie' => 'required|date',
            'instruction' => 'nullable|string|max:500',
            'comment' => 'nullable|string|max:800',
        ]);
        $validated['qte_reel'] = $validated['qte_reel'] ?? 0;
        $validated['priority'] = $validated['priority'] ?? 0;
        $validated['qty_produced'] = $validated['qty_produced'] ?? 0;
        $validated['Priority'] = $validated['priority'];
        unset($validated['priority']);
        if ($request->filled('id')) {
            $of = Ofplanning::findOrFail($request->id);
            $of->update($validated);
        } else {
            Ofplanning::create($validated);
        }

        return response()->json(['message' => 'Commande enregistrée avec succès.']);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $of = Ofplanning::findOrFail($id);
        return response()->json($of);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $of = Ofplanning::findOrFail($id);
        $of->delete();

        return response()->json(['message' => 'Commande supprimée avec succès.']);
    }

}
