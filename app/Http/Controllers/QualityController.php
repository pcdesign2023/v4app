<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Quality;
use App\Http\Controllers\Auth;
use App\Models\Chaine;
use App\Models\FabOrder;
use App\Models\DefaultEntry;
use App\Models\Anomaly;
use Illuminate\Http\Request;
use App\Models\User;

class QualityController extends Controller
{

    public function index()
    {
        $user = auth()->user(); // Get the authenticated user

$fabOrders = FabOrder::with(['chaine', 'quality.anomaly', 'client'])
            ->withSum('conformityDetails as totalQtyC', 'Qty_NC')
            ->get();
        if (in_array($user->role_id, [1, 3])) {
            // Administrateur (1) & QualityResp (2) can see all orders
            //$fabOrders = $fabOrders->get();
        } elseif ($user->role_id == 3) {
            // Quality Agent (3) only sees orders where their Chaine has them as responsable_QLTY
            $fabOrders = $fabOrders
                ->whereHas('chaine', function ($query) use ($user) {
                    $query->where('responsable_QLTY_id', $user->id);
                })
                ->get();
        } else {
            // No orders for unauthorized users
            $fabOrders = collect();
        }
        $anomalies = Anomaly::all();
        $chaines = Chaine::all();
        $clients = Client::all();
        $defaulteentry = DefaultEntry::all();
        // Load all products for the component dropdown
        $products = Product::select('id', 'ref_id', 'component_name', 'component_code')
            ->get();
        // Pass productRefId for JavaScript use if available
        $productRefIds = $fabOrders->pluck('product.ref_id')->filter()->unique()->values();

        $pendingQualityCount = Quality::whereHas('fabOrder', function ($query) {
            $query->where('Statut_of', 'pending');
        })->count();

        return view('quality.index', compact('fabOrders','clients','chaines','productRefIds', 'anomalies','products', 'pendingQualityCount', 'defaulteentry'));
    }



    public function store(Request $request)
    {

    }

    public function update(Request $request, Quality $quality)
    {
        $request->validate([
            'QtyConform' => 'required|integer|min:0',
            'QtyNConform' => 'required|integer|min:0',
            'RespDefaut' => 'required|string',
            'DateInterv' => 'nullable|date',
            'Qlty_comment' => 'nullable|string',
        ]);

        $quality->update($request->all());

        return redirect()->route('quality.index')->with('success', 'Quality ticket updated successfully.');
    }

    public function destroy(Quality $quality)
    {
        $quality->delete();
        return redirect()->route('quality.index')->with('success', 'Quality ticket deleted successfully.');
    }
    public function storeOrUpdate(Request $request)
    {
        dd($request->all());
        $request->validate([
            'chaineID' => 'required|exists:chaine,id',
            'OF_ID' => 'required|exists:fab_orders,id',
            'AnoID' => 'required|exists:anomalies,AnoID',
            'QtyConform' => 'required|integer|min:0',
            'fk_OFID' => 'required|integer|min:0',
            'QtyNConform' => 'required|integer|min:0',
            'RespDefaut' => 'required|string|in:Operator,Machine,Process',
            'DateInterv' => 'nullable|date',
            'Qlty_comment' => 'nullable|string',
        ]);

        // Check if quality record exists for this OF_ID
        $quality = Quality::where('OF_ID', $request->OF_ID)->first();

        if ($quality) {
            // Update existing record
            $quality->update($request->all());
        } else {
            // Create new quality record

            Quality::create([
                'chaineID' => $request->chaineID,
                'OF_ID' => $request->OF_ID,
                'fk_OFID' => $request->fk_OFID,
                'AnoID' => $request->AnoID,
                'QtyConform' => $request->QtyConform,
                'QtyNConform' => $request->QtyNConform,
                'RespDefaut' => $request->RespDefaut,
                'DateInterv' => $request->DateInterv,
                'Qlty_comment' => $request->Qlty_comment,
            ]);        }

        return redirect()->route('quality.index')->with('success', 'Quality ticket saved successfully.');
    }

}
