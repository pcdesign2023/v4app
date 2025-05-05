<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Chaine;
use App\Models\User;

class ChaineController extends Controller
{
    public function index()
    {
        $chaines = Chaine::with(['responsableQLTY', 'chefDeChaine'])->get();

        // Fetch users with specific roles
        $responsablesQLTY = User::whereHas('role', function ($query) {
            $query->where('label', 'Agent qualité');
        })->get();

        $chefsDeChaine = User::whereHas('role', function ($query) {
            $query->where('label', 'Chef de Chaine');
        })->get();

        return view('chaine-management.index', compact('chaines', 'responsablesQLTY', 'chefsDeChaine'));
    }

    public function destroy(Chaine $chaine)
    {
        $chaine->delete();

        return redirect()->route('chaine.index')->with('success', 'Chaine supprimer avec succès.');
    }
    public function update(Request $request, Chaine $chaine)
    {
        $request->validate([
            'Num_chaine' => 'required|integer|unique:chaine,Num_chaine,' . $chaine->id,
            'responsable_QLTY_id' => 'nullable|exists:users,id',
            'chef_de_chaine_id' => 'nullable|exists:users,id',
            'nbr_operateur' => 'nullable|integer|min:1',
        ]);

        $chaine->update($request->all());

        return redirect()->route('chaine.index')->with('success', 'Chaine modifié avec succès.');
    }
    public function store(Request $request)
    {
        $request->validate([
            'Num_chaine' => 'required|integer|unique:chaine,Num_chaine',
            'responsable_QLTY_id' => 'nullable|exists:users,id',
            'chef_de_chaine_id' => 'nullable|exists:users,id',
            'nbr_operateur' => 'required|integer|min:1',
        ]);

        $users = User::all(); // Fetch all users for dropdowns
        Chaine::create($request->all());
        return redirect()->route('chaine.index')->with('success', 'Chaine créer succès.');
    }
}
