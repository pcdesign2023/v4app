@extends('layouts.master')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">liste des produits</h4>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                   <!-- <div class="d-flex justify-content-between">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Add Product</button>
                    </div> -->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="products-table">
                            <thead>
                            <tr>
                                <th>REFERENCE</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Ref ID</label>
                            <input type="text" name="ref_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BOM Modal -->
    <div class="modal fade" id="bomModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nomenclature produit</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Descrption</th>
                            <th>Quantité</th>
                        </tr>
                        </thead>
                        <tbody id="bom-table-body">
                        <tr><td colspan="3">Select a product to view BOM</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js')}}"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            if (!$.fn.DataTable.isDataTable('#products-table')) {
                $('#products-table').DataTable({
                    processing: true,
                    serverSide: true,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                    },
                    ajax: "{{ route('products.index') }}",
                    destroy: true, // ✅ Allow reinitialization
                    columns: [
                        { data: 'ref_id', name: 'ref_id' },
                        { data: 'product_name', class: 'bom-btn' ,name: 'product_name' },
                        {
                            data: 'id',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            render: function(id, type, row) {
                                return `

                                    <button class="btn btn-info btn-sm bom-btn" data-ref_id="${row.ref_id}">
                                        Nomenclature
                                    </button>
                                    `;
                            }
                        }
                    ]
                });
            }

            // Handle BOM Modal
            $(document).on('click', '.bom-btn', function() {
                let ref_id = $(this).data('ref_id');

                // Show loading text
                $('#bom-table-body').html('<tr><td colspan="3">Loading...</td></tr>');

                // Fetch BOM data via AJAX
                $.ajax({
                    url: "{{ route('products.bom') }}",
                    type: "GET",
                    data: { ref_id: ref_id },
                    success: function (response) {
                        let bomHtml = "";
                        if (response.length > 0) {
                            response.forEach(function (item) {
                                bomHtml += `<tr>
                                                <td>${item.component_code}</td>
                                                <td>${item.component_name}</td>
                                                <td>${item.Quantity}</td>
                                            </tr>`;
                            });
                        } else {
                            bomHtml = "<tr><td colspan='3'>No BOM data found</td></tr>";
                        }
                        $('#bom-table-body').html(bomHtml);
                    },
                    error: function () {
                        $('#bom-table-body').html("<tr><td colspan='3'>Error loading data</td></tr>");
                    }
                });

                $('#bomModal').modal('show');
            });

            // Handle Update Modal
            $(document).on('click', '.update-btn', function() {
                let id = $(this).data('id');
                let ref = $(this).data('ref');
                let name = $(this).data('name');

                let modalHtml = `
                    <div class="modal fade" id="updateProductModal${id}" tabindex="-1" role="dialog">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ url('/products') }}/${id}" method="POST">
                                    @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Update Product</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ref ID</label>
                        <input type="text" name="ref_id" class="form-control" value="${ref}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Product Name</label>
                                            <input type="text" name="product_name" class="form-control" value="${name}" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Update</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                `;

                $('body').append(modalHtml);
                $(`#updateProductModal${id}`).modal('show');
            });

        });
    </script>
@endsection
