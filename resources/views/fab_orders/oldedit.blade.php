@extends('layouts.master')

@section('css')
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .form-group label {
            font-weight: bold;
        }
        .btn-success:hover {
            background-color: #28a745;
            opacity: 0.9;
        }
        .btn-secondary:hover {
            opacity: 0.8;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 18px;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .card-footer {
            background-color: #f1f3f5;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <h4 class="content-title mb-0">Édition d'Ordre de Fabrication</h4>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mt-4">

        {{-- Display Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="card">
            <div class="card-header">
                📝 Modifier l'Ordre de Fabrication
            </div>
            <div class="card-body">
                {{-- FORM START --}}
                <form action="{{ route('fab_orders.update', $fabOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">

                        {{-- OF ID --}}
                        <div class="col-md-6 form-group">
                            <label for="OFID">OF ID <span class="text-danger">*</span></label>
                            <input type="text" id="OFID" name="OFID" class="form-control"
                                   value="{{ old('OFID', $fabOrder->OFID) }}" placeholder="Enter OF ID" required>
                        </div>

                        {{-- Produit --}}
                        <div class="col-md-6 form-group">
                            <label for="Prod_ID">Produit <span class="text-danger">*</span></label>
                            <select name="Prod_ID" id="Prod_ID" class="form-control select2" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ $product->id == old('Prod_ID', $fabOrder->Prod_ID) ? 'selected' : '' }}>
                                        {{ $product->ref_id }} - {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Chaine --}}
                        <div class="col-md-3 form-group">
                            <label for="chaineID">Chaine <span class="text-danger">*</span></label>
                            <select id="chaineID" name="chaineID" class="form-control" required>
                                <option value="">Select Chaine</option>
                                @foreach ($chaines as $chaine)
                                    <option value="{{ $chaine->id }}"
                                        {{ $chaine->id == $fabOrder->chaineID ? 'selected' : '' }}>
                                        {{ $chaine->Num_chaine }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Commande --}}
                        <div class="col-md-3 form-group">
                            <label for="saleOrderId">Commande <span class="text-danger">*</span></label>
                            <input type="text" id="saleOrderId" name="saleOrderId" class="form-control"
                                   value="{{ old('saleOrderId', $fabOrder->saleOrderId) }}" placeholder="Enter sale order ID" required>
                        </div>

                        {{-- Client --}}
                        <div class="col-md-6 form-group">
                            <label for="client_id">Client <span class="text-danger">*</span></label>
                            <select id="client_id" name="client_id" class="form-control select2" required>
                                <option value="">Select Client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-instruction="{{ $client->instruction }}"
                                        {{ $client->id == old('client_id', $fabOrder->client_id) ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Lot SET --}}
                        <div class="col form-group">
                            <label for="Lot_Set">Lot SET <span class="text-danger">*</span></label>
                            <input type="text" id="Lot_Set" name="Lot_Set" class="form-control"
                                   value="{{ old('Lot_Set', $fabOrder->Lot_Set) }}" placeholder="Enter Lot SET" required>
                        </div>
                        {{-- PF Qty --}}
                        <div class="col form-group">
                            <label for="Pf_Qty">PF Quantité <span class="text-danger">*</span></label>
                            <input type="number" id="Pf_Qty" name="Pf_Qty" class="form-control"
                                   value="{{ old('Pf_Qty', $fabOrder->Pf_Qty) }}" placeholder="PF Qty" required>
                        </div>

                        {{-- SF Qty --}}
                        <!--<div class="col-md-3 form-group">
                            <label for="Sf_Qty">SF Quantité <span class="text-danger">*</span></label>
                            <input type="number" id="Sf_Qty" name="Sf_Qty" class="form-control"
                                   value="{{ old('Sf_Qty', $fabOrder->Sf_Qty) }}" placeholder="SF Qty" required>
                        </div> -->

                        {{-- SET Qty --}}
                        <div class="col form-group">
                            <label for="Set_qty">SET Quantité <span class="text-danger">*</span></label>
                            <input type="number" id="Set_qty" name="Set_qty" class="form-control"
                                   value="{{ old('Set_qty', $fabOrder->Set_qty) }}" placeholder="SET Qty" required>
                        </div>

                        {{-- Tester Qty --}}
                        <div class="col form-group">
                            <label for="Tester_qty">Tester Quantité <span class="text-danger">*</span></label>
                            <input type="number" id="Tester_qty" name="Tester_qty" class="form-control"
                                   value="{{ old('Tester_qty', $fabOrder->Tester_qty) }}" placeholder="Tester Qty" required>
                        </div>



                        {{-- Date Fabrication --}}
                        <div class="col-md-6 form-group">
                            <label for="date_fabrication">Date de Planification</label>
                            <input type="datetime-local" id="date_fabrication" name="date_fabrication" class="form-control"
                                   value="{{ old('date_fabrication', $fabOrder->date_fabrication ? \Carbon\Carbon::parse($fabOrder->date_fabrication)->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        {{-- Instruction --}}
                        <div class="col-md-12 form-group">
                            <label for="instruction">Instruction</label>
                            <textarea id="instruction" name="instruction" class="form-control" rows="3"
                                      placeholder="Enter instructions">{{ old('instruction', $fabOrder->instruction ?? '') }}</textarea>
                        </div>
                        {{-- Comment Field --}}
                        <div class="col-md-12 form-group">
                            <label for="comment">Commentaire</label>
                            <textarea  id="comment" name="comment" class="form-control" rows="3"
                                   placeholder="Enter a comment">{{ old('comment', $fabOrder->comment ?? '') }}</textarea>
                        </div>


                        {{-- Statut --}}
                        <div class="col-md-6 form-group">
                            <label for="Statut_of">Statut <span class="text-danger">*</span></label>
                            <select id="Statut_of" name="Statut_of" class="form-control" required>
                                <option value="Planifié" {{ old('Statut_of', $fabOrder->Statut_of) === 'Planifié' ? 'selected' : '' }}>Planifié</option>
                                <option value="En cours" {{ old('Statut_of', $fabOrder->Statut_of) === 'En cours' ? 'selected' : '' }}>En cours</option>
                                <option value="Réalisé" {{ old('Statut_of', $fabOrder->Statut_of) === 'Réalisé' ? 'selected' : '' }}>Réalisé</option>
                                <option value="Suspendu" {{ old('Statut_of', $fabOrder->Statut_of) === 'Suspendu' ? 'selected' : '' }}>Suspendu</option>
                            </select>
                        </div>

                    </div> {{-- End of Row --}}

                    {{-- Buttons INSIDE the form (Fixed) --}}
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Modifier
                        </button>
                        <a href="{{ route('fab_orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </a>
                    </div>
                </form>
                {{-- FORM END --}}
            </div>
        </div> {{-- End of Card --}}
    </div>
@endsection

@section('js')
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Select2 with search functionality
            $('.select2').select2({
                placeholder: "Please select",
                allowClear: true,
                width: '100%'
            });

            // Auto-fill instructions when a client is selected
            $('#client_id').change(function () {
                var selectedOption = $(this).find(':selected');
                var instruction = selectedOption.data('instruction');
                $('#instruction').val(instruction);
            });
        });
    </script>
@endsection
