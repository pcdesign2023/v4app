<?php
namespace App\Http\Controllers;

use App\Models\QualityCheck;
use App\Models\QualityDefect;
use App\Models\FabOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QualityCheckController extends Controller
{
    public function index()
    {
        $qualityChecks = QualityCheck::with('fabricationOrder')->latest()->get();
        return view('quality_checks.index', compact('qualityChecks'));
    }

    public function create()
    {
        $fabricationOrders = FabOrder::all();
        $products = Product::all();
        $defectTypes = ['cassé', 'rayure', 'dimension'];

        return view('quality_checks.create', compact('fabricationOrders', 'products', 'defectTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fabrication_order_id' => 'required|exists:fabrication_orders,id',
            'quantity_conform' => 'required|integer|min:0',
            'quantity_nonconform' => 'required|integer|min:0',
            'defects.*.defect_type' => 'required|string',
            'defects.*.product_component_id' => 'required|exists:products,id',
            'defects.*.quantity' => 'required|integer|min:1',
        ]);

        $check = QualityCheck::create([
            'fabrication_order_id' => $request->fabrication_order_id,
            'quantity_conform' => $request->quantity_conform,
            'quantity_nonconform' => $request->quantity_nonconform,
            'checked_by_user_id' => Auth::id(),
        ]);

        foreach ($request->defects as $defect) {
            QualityDefect::create([
                'quality_check_id' => $check->id,
                'defect_type' => $defect['defect_type'],
                'product_component_id' => $defect['product_component_id'],
                'quantity' => $defect['quantity'],
            ]);
        }

        return redirect()->route('quality_checks.index')->with('success', 'Quality check saved.');
    }
}
