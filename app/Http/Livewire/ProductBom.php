<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductBom extends Component
{
    protected $listeners = ['loadBOM' => 'fetchBOM', 'testEvent' => 'testResponse'];

    public function testResponse()
    {
        dd('Livewire is connected!');
    }

    public $productId;
    public $productName;
    public $bomMaterials = [];
    public $showModal = false;

    // Listen for an event to load BOM
    protected $listeners = ['loadBOM' => 'fetchBOM'];

    /**
     * Fetch BOM materials from the products table.
     */
    public function fetchBOM($productId, $productName)
    {
        $this->productId = $productId;
        $this->productName = $productName;

        // Query BOM from products table (excluding parent product)
        $this->bomMaterials = Product::where('ref_id', $productId)
            ->where('id', '!=', $productId) // Exclude the parent product
            ->whereNotNull('component_name') // Only rows that have component details
            ->select('component_name', 'component_code', 'quantity')
            ->get();

        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.product-bom');
    }

}
