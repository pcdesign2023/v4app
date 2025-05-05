<?php
namespace App\Http\Controllers;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::select('product_name', DB::raw('MIN(id) as id'), DB::raw('MIN(ref_id) as ref_id'))
                ->groupBy('product_name'); // ✅ Grouping by product_name

            return DataTables::of($products)->make(true);
        }

        return view('products.index');
    }
    public function getBOM(Request $request)
    {
        $ref_id = $request->query('ref_id');

        $bomData = Product::select('component_code', 'component_name', 'Quantity')
            ->where('ref_id', $ref_id)
            ->get();

        return response()->json($bomData);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref_id' => 'required|string|unique:products,ref_id',
            'product_name' => 'required|string',
            'component_name' => 'nullable|string',
            'component_code' => 'nullable|string',
            'Quantity' => 'nullable|numeric|min:0',
        ]);


        Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'ref_id' => 'required|unique:products,ref_id,' . $product->id,
            'product_name' => 'required',
            'component_name' => 'nullable|string',
            'component_code' => 'nullable|string',
            'Quantity' => 'nullable|numeric|min:0',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
