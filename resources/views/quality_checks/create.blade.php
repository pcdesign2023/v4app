@extends('layouts.master')

@section('css')
    <!-- Add any page-specific CSS here -->
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Quality</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Add Quality Check</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title mb-1">New Quality Check</h4>
                </div>
                <div class="card-body pt-0">
                    <form action="{{ route('quality_checks.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Fabrication Order</label>
                            <select name="fabrication_order_id" class="form-control" required>
                                <option value="">Select</option>
                                @foreach($fabricationOrders as $fo)
                                    <option value="{{ $fo->id }}">{{ $fo->id }} - {{ $fo->product->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantity Conform</label>
                            <input type="number" name="quantity_conform" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Quantity Non-Conform</label>
                            <input type="number" name="quantity_nonconform" class="form-control" required>
                        </div>

                        <hr>
                        <h5>Defects</h5>
                        <div id="defects-list"></div>
                        <button type="button" onclick="addDefectRow()" class="btn btn-sm btn-secondary mt-2">+ Add Defect</button>

                        <hr>
                        <button type="submit" class="btn btn-success mt-2">Save Quality Check</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection

@section('js')
    <script>
        let defectTypes = @json($defectTypes);
        let products = @json($products);
        let index = 0;

        function addDefectRow() {
            let html = `<div class="row mb-2" id="defect_${index}">
			<div class="col-md-3">
				<select name="defects[${index}][defect_type]" class="form-control" required>
					${defectTypes.map(dt => `<option value="${dt}">${dt}</option>`).join('')}
				</select>
			</div>
			<div class="col-md-5">
				<select name="defects[${index}][product_component_id]" class="form-control" required>
					${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
				</select>
			</div>
			<div class="col-md-2">
				<input type="number" name="defects[${index}][quantity]" class="form-control" placeholder="Qty" required>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-sm btn-danger" onclick="removeDefectRow(${index})">X</button>
			</div>
		</div>`;
            document.getElementById('defects-list').insertAdjacentHTML('beforeend', html);
            index++;
        }

        function removeDefectRow(i) {
            document.getElementById(`defect_${i}`).remove();
        }
    </script>
@endsection
