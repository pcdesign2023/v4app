<?php
namespace App\Http\Controllers;
use App\Imports\FabOrdersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\FabOrder;
use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Models\Chaine;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FabOrderController extends Controller
{

    public function index()
    {
        $fabOrders = FabOrder::with(['product', 'chaine','client'])
            ->where("Statut_of",'=','En cours')
            ->orWhere("Statut_of",'=','Planifié')
            ->orWhere("Statut_of",'=','Réalisé')
            ->orderBy('creation_date_Of', 'desc') // Sort by creation_date_Of in descending order
            ->get();
             $chaines = Chaine::with('chef')->get();
        return view('fab_orders.index', compact('fabOrders', 'chaines'));
    }
   public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx',
        ]);

        Excel::import(new FabOrdersImport, $request->file('excel_file'));

        return redirect()->route('fab_orders.index')->with('success', 'Importation réussie des ordres de fabrication.');
    }
    public function create()
    {
        $products = Product::selectRaw('MIN(id) as id, product_name,ref_id')
            ->groupBy('product_name','ref_id')
            ->get();
        $chaines = Chaine::all();
        $users = User::all();
        $clients = Client::all();
        return view('fab_orders.create', compact('products', 'chaines','users', 'clients'));
    }
    public function updateStatus($OFID)
    {
        $decodedOFID = urldecode($OFID); // Decode the OFID from the URL

        $fabOrder = FabOrder::where('OFID', $decodedOFID)->first();

        if (!$fabOrder) {
            return response()->json(['error' => 'FabOrder not found.'], 404);
        }

        // ✅ Update the status to "Réalisé"
        $fabOrder->update(['Statut_of' => 'Réalisé']);

        return response()->json(['success' => 'Statut updated successfully.', 'new_status' => 'Réalisé']);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $totalCreated = 0;
            $errors = [];

            // Loop through all request fields and save orders one by one
            foreach ($request->all() as $key => $value) {
                if (str_starts_with($key, 'OFID_')) {
                    $index = explode('_', $key)[1]; // Get form index

                    // Validate individually using the old rules
                    $validator = Validator::make([
                        'OFID' => $request->input("OFID_$index"),
                        'Prod_ID' => $request->input("Prod_ID_$index"),
                        'client_id' => $request->input("client_id_$index"),
                        'saleOrderId' => $request->input("saleOrderId_$index"),
                        'date_fabrication' => $request->input("date_fabrication_$index"),
                    ], [
                        'OFID' => 'required|string|unique:fab_orders,OFID',
                        'Prod_ID' => 'required|exists:products,id',
                        'client_id' => 'required|exists:clients,id',
                        'saleOrderId' => 'required|string',
                        'date_fabrication' => 'required|date',
                    ]);

                    if ($validator->fails()) {
                        $errors["Formulaire #$index"] = $validator->errors()->all();
                        continue; // Skip if validation fails
                    }

                    // Save one by one
                    FabOrder::create([
                        'OFID' => $request->input("OFID_$index"),
                        'Prod_ID' => $request->input("Prod_ID_$index"),
                        'chaineID' => $request->input("chaineID_$index"),
                        'client_id' => $request->input("client_id_$index"),
                        'saleOrderId' => $request->input("saleOrderId_$index"),
                        'Lot_Set' => $request->input("Lot_Set_$index"),
                        'Pf_Qty' => $request->input("Pf_Qty_$index") ?? 0,
                        'Set_qty' => $request->input("Set_qty_$index") ?? 0,
                        'Tester_qty' => $request->input("Tester_qty_$index") ?? 0,
                        'Sf_Qty' => $request->input("Sf_Qty_$index") ?? 0,
                        'date_fabrication' => $request->input("date_fabrication_$index"),
                        'Comment' => $request->input("Comment_$index"),
                        'instruction' => $request->input("instruction_$index"),
                    ]);
                    $totalCreated++;
                }
            }

            if (!empty($errors)) {
                // Show validation errors and return to the form
                return redirect()->back()->withErrors($errors)->withInput();
            }

            DB::commit();
            return redirect()->route('fab_orders.index')->with('success', "$totalCreated Ordres de fabrication créés avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la création des ordres: ' . $e->getMessage());
        }
    }

    public function edit(FabOrder $fabOrder)
    {
        $products = Product::selectRaw('MIN(id) as id, product_name,ref_id')
            ->groupBy('product_name','ref_id')
            ->get();        $chaines = Chaine::all();
        $clients = Client::all();

        return view('fab_orders.edit', compact('fabOrder', 'products', 'chaines', 'clients'));
    }
    public function update(Request $request, FabOrder $fabOrder)
    {
        $request->validate([
            'OFID' => 'required|unique:fab_orders,OFID,' . $fabOrder->id,
            'Prod_ID' => 'required|exists:products,id',
            'chaineID' => 'required|exists:chaine,id',
            'saleOrderId' => 'required',
            'date_fabrication' => 'required|date',
            'client_id' => 'required|exists:clients,id',
            'Pf_Qty' => 'nullable|integer|min:0',
            'Sf_Qty' => 'nullable|integer|min:0',
            'Set_qty' => 'nullable|integer|min:0',
            'Tester_qty' => 'nullable|integer|min:0',
            'Lot_Set' => 'nullable|string',
            'instruction' => 'nullable|string',
            'Statut_of' => 'nullable|string',
        ]);

        $fabOrder->update($request->all());

        return redirect()->route('fab_orders.index')->with('success', 'Fabrication Order updated successfully.');
    }


    public function getProductComponents($productId): JsonResponse
    {
        // Find the product by ID
        $product = Product::where('id', $productId)->first();

        if (!$product) {
            return response()->json(['components' => []]); // Return empty if not found
        }

        // Format response to match component structure
        $components = [
            [
                'component_name' => $product->component_name ?? 'Not Provided',
                'component_code' => $product->component_code ?? 'Not Provided',
                'quantity' => 1 // Since quantity is not in DB, assume 1
            ]
        ];

        return response()->json(['components' => $components]);
    }
    public function fabChain()
    {
        // Load all fabrication orders with related 'chaine', 'chef' (user), and 'product'
        $fabOrders = FabOrder::with(['chaine.chef', 'product'])
            ->latest('creation_date_Of') // Sort by most recent first
            ->get();

        // Retrieve all Chaines with their assigned Chef (User)
        $chaines = Chaine::with('chef')->get();

        // Retrieve all Users who are assigned as Chef de Chaine
        $users = User::whereHas('chaine')->get();

        // Pass all required variables to the view
        return view('fab_chaine.index', compact('fabOrders', 'chaines', 'users'));
    }





    public function editFabChain(FabOrder $fabOrder)
    {
        return view('fab_chaine.edit_fab_chain', compact('fabOrder'));
    }

    public function updateFabChain(Request $request, FabOrder $fabOrder)
    {
        $request->validate([
            'Lot_Jus' => 'nullable|string',
            'Valid_date' => 'nullable|date',
            'instruction' => 'nullable|string',
            'Comment_chaine' => 'nullable|string',
            'Start_Prod' => 'nullable|date',
            'End_Prod' => 'nullable|date',
            'Qty_fabrique' => 'nullable|integer|min:0',
            'Statut_of' => 'required|string|max:40',
        ]);

        $fabOrder->update($request->all());

        return redirect()->route('fab_chain.index')->with('success', 'Fabrication Order updated successfully.');
    }

    public function destroy(FabOrder $fabOrder)
{
    $fabOrder->conformityDetails()->delete(); // delete children first
    $fabOrder->delete(); // then delete parent
    return redirect()->route('fab_orders.index')->with('success', 'Fab Order deleted successfully.');
}



}
