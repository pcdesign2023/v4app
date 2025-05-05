@extends('layouts.master')

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/custom-of-form.css') }}">

@endsection

@section('content')
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-md-4">
                <input type="number" id="formCount" placeholder="Nombre d'OF à ajouter" class="form-control" min="1">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-outline-primary mb-1" onclick="addOFForms()">Ajouter plus d'OF</button>
                <a href="{{ route('fab_orders.index') }}" type="button" class="btn btn-outline-secondary mb-1" >
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>

            </div>
        </div>

        <form id="bulkForm" action="{{ route('fab_orders.store') }}" method="POST">
            @csrf
            <div id="orderFormsContainer">
                <!-- Show one form by default -->
                <div class="card form-card mb-4 p-3 border border-primary" id="form_1">
                    <h5 class="mb-3">OF #1</h5>
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <label>Référence OF:</label>
                            <input type="text" name="OFID_1" placeholder="OF reference" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Produit:</label>
                            <select name="Prod_ID_1" class="form-control" onchange="updateProductFields(this, 1)">
                                <option value="">Sélectionner un produit</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->product_name }}" data-ref="{{ $product->ref_id }}">
                                        {{ $product->ref_id }} - {{ $product->product_name }}
                                    </option>
                                @endforeach
                            </select>

                            <input type="hidden" name="prod_name_1" id="prod_name_1">
                            <input type="hidden" name="prod_ref_1" id="prod_ref_1">

                        </div>
                        <div class="col-md-4">
                            <label>Chaine:</label>
                            <select name="chaineID_1" class="form-control" required>
                                <option value="">Sélectionner une chaîne</option>
                                @foreach ($chaines as $chaine)
                                    <option value="{{ $chaine->id }}">{{ $chaine->Num_chaine }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Client:</label>
                            <select name="client_id_1" class="form-control select2" onchange="setClientInstruction(this)" required>
                                <option value="">Sélectionner un client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-instruction="{{ $client->instruction }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label>Sale Order:</label>
                            <input type="text" name="saleOrderId_1" placeholder="Sale Order" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Lot Set:</label>
                            <input type="text" name="Lot_Set_1" placeholder="Lot Set" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Quantités:</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" value="0" placeholder="PF" name="Pf_Qty_1" class="form-control">
                                    <small class="text-muted">PF</small>
                                </div>
                                <div class="col">
                                    <input type="number" value="0" placeholder="SET" name="Set_qty_1" class="form-control">
                                    <small class="text-muted">SET</small>
                                </div>
                                <div class="col">
                                    <input type="number" value="0" placeholder="TESTER" name="Tester_qty_1" class="form-control">
                                    <small class="text-muted">TESTER</small>
                                </div>
                                <div class="col">
                                    <input style="display:none" type="number" value="0" placeholder="SF" name="Sf_Qty_1" class="form-control">
                                    <small style="display:none" class="text-muted">SF</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Date prévue de fabrication:</label>
                            <input type="datetime-local" name="date_fabrication_1" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Commentaire:</label>
                            <textarea name="Comment" placeholder="Commentaire" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label>Instruction:</label>
                            <textarea name="instruction_1" placeholder="Instruction" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Button -->
            <div class="mt-3">
                <button type="submit" class="btn btn-success btn-lg">Créer les Ordres de fabrication</button>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script>
        let formCounter = 2;

        function addOFForms() {
            const count = $('#formCount').val();
            const container = $('#orderFormsContainer');

            if (count <= 0) {
                alert('Veuillez saisir un nombre valide.');
                return;
            }

            for (let i = 0; i < count; i++) {
                let formHtml = `
            <div class="card form-card mb-4 p-3 border border-secondary" id="form_${formCounter}">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>OF #${formCounter}</h5>
                    <span class="remove-btn" onclick="removeForm(${formCounter})">Supprimer</span>
                </div>
                <div class="row gy-3">
                    <div class="col-md-4">
                        <label>Référence OF:</label>
                        <input type="text" name="OFID_${formCounter}" placeholder="OF reference" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Produit:</label>
                        <select name="Prod_ID_${formCounter}" class="form-control select2" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->ref_id }} - {{ $product->product_name }}</option>
                            @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Chaine:</label>
                <select name="chaineID_${formCounter}" class="form-control" required>
                            <option value="">Sélectionner une chaîne</option>
                            @foreach ($chaines as $chaine)
                <option value="{{ $chaine->id }}">{{ $chaine->Num_chaine }}</option>
                            @endforeach
                </select>
            </div>
            <div class="col-md-4">
    <label>Client:</label>
    <select name="client_id_1" class="form-control select2" onchange="setClientInstruction(this)" required>
        <option value="">Sélectionner un client</option>
        @foreach ($clients as $client)
                <option value="{{ $client->id }}" data-instruction="{{ $client->instruction }}">{{ $client->name }}</option>
        @endforeach
                </select>
            </div>

                        <div class="col-md-4">
                            <label>Sale Order:</label>
                            <input type="text" name="saleOrderId_${formCounter}" placeholder="Sale Order" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Lot Set:</label>
                        <input type="text" name="Lot_Set_${formCounter}" placeholder="Lot Set" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Quantités:</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" value="0" placeholder="PF" name="Pf_Qty_${formCounter}" class="form-control">
                                <small class="text-muted">PF</small>
                            </div>
                            <div class="col">
                                <input type="number" value="0" placeholder="SET" name="Set_qty_${formCounter}" class="form-control">
                                <small class="text-muted">SET</small>
                            </div>
                            <div class="col">
                                <input type="number" value="0" placeholder="TESTER" name="Tester_qty_${formCounter}" class="form-control">
                                <small class="text-muted">TESTER</small>
                            </div>
                            <div class="col">
                                <input type="number" style="display:none" value="0" placeholder="SF" name="Sf_Qty_${formCounter}" class="form-control">
                                <small style="display:none"  class="text-muted">SF</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Date prévue de fabrication:</label>
                        <input type="datetime-local" name="date_fabrication_${formCounter}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Commentaire:</label>
                        <textarea name="Comment_${formCounter}" placeholder="Commentaire" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Instruction:</label>
                        <textarea name="instruction_${formCounter}" placeholder="Instruction" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            `;
                container.append(formHtml);
                $('.select2').select2(); // Reinitialize Select2
                formCounter++;
            }
        }

        function removeForm(id) {
            $(`#form_${id}`).fadeOut(300, function() {
                $(this).remove();
            });
        }

        function setClientInstruction(selectElement) {
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var instruction = selectedOption.getAttribute('data-instruction');
            var instructionField = selectElement.closest('.row').querySelector('textarea[name^="instruction"]');
            if (instructionField) {
                instructionField.value = instruction || '';
            }
        }

        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Sélectionner",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <script>
        function updateProductFields(select, index) {
            const selectedOption = select.options[select.selectedIndex];
            const name = selectedOption.getAttribute('data-name');
            const ref = selectedOption.getAttribute('data-ref');

            console.log(`Index ${index}:`, name, ref); // 👈 Check console on change

            document.getElementById(`prod_name_${index}`).value = name;
            document.getElementById(`prod_ref_${index}`).value = ref;
        }
    </script>
@endsection
