@extends('layouts.master')

@section('css')
<style>
        .form-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .order-summary {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .order-summary table {
            margin-bottom: 0;
        }

        .required-field::after {
            content: '*';
            color: #dc3545;
            margin-left: 4px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .quantity-input {
            position: relative;
        }

        .quantity-input .form-control[disabled] {
            background-color: #f8fafc;
            cursor: not-allowed;
        }

        .btn-submit {
            background: #0d6efd;
            border: none;
            padding: 0.75rem 2rem;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: #0b5ed7;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .order-summary {
                overflow-x: auto;
            }

            .btn-group {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group .btn {
                width: 100%;
            }
        }
</style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between mb-4">

    </div>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('fabrication.store') }}" method="POST" id="fabricationForm">
            @csrf
            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class=" ml-auto  content-title mb-0 my-auto">Déclaration de production  OF:  <a class=" text-white bg-primary"> &nbsp;{{ $fabOrder->OFID }} &nbsp;</a></h4>
                </div>
                <div>
                    <a href="{{ route('fab_chain.index') }}" class="btn btn-outline-secondary mb-1">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-submit text-white bg-primary ml-1 mb-1">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="form-card order-summary">
                <div class="table-responsive">
                    <table class="table table-bordered mg-b-0">
                        <thead class="bg-light">
                        <tr>
                            <th>Client</th>
                            <th>Produit</th>
                            <th>Instructions</th>
                            <th>PF</th>
                            <th>Testeur</th>
                            <th>Set</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ old('client_Name', $fabOrder->client->name ?? 'N/A') }}</td>
                            <td>{{ old('product_Name', $fabOrder->product->product_name ?? 'N/A') }}</td>
                            <td>{{ old('Comment_OF', $fabOrder->instruction ?? 'N/A') }}</td>
                            <td><span style="font-size: 0.8rem !important;"class="badge bg-primary text-white">{{ old('Pf_Qty', $fabOrder->Pf_Qty) }}</span></td>
                            <td><span style="font-size: 0.8rem !important;" class="badge bg-primary text-white">{{ old('Tester_qty', $fabOrder->Tester_qty) }}</span></td>
                            <td><span style="font-size: 0.8rem !important;" class="badge bg-primary text-white">{{ old('Set_qty', $fabOrder->Set_qty) }}</span></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Basic Information Card -->
            <div style="display: none" class="form-card">
                <h5 class="mb-3">Informations de Base</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="OFID" class="form-label">Numéro OF</label>
                            <input type="text" id="OFID" name="OFID" class="form-control bg-light"
                                   value="{{ $fabOrder->OFID }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        DDDDD
                    </div>
                </div>
            </div>

            <!-- Production Details Card -->
            <div class="form-card">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Valid_date" class="form-label">Date de Validité</label>
                            <input type="date" id="Valid_date" name="Valid_date" class="form-control"
                                   value="{{ old('Valid_date', $fabOrder->Valid_date ? \Carbon\Carbon::parse($fabOrder->Valid_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_fabrication" class="form-label required-field">Début de Production</label>
                            <input type="datetime-local" id="date_fabrication" name="date_fabrication"
                                   class="form-control @error('date_fabrication') is-invalid @enderror"
                                   value="{{ old('date_fabrication', $fabOrder->Start_Prod ? \Carbon\Carbon::parse($fabOrder->Start_Prod)->format('Y-m-d\TH:i') : '') }}"
                                   required>
                            @error('date_fabrication')
                            <div class="invalid-feedback">La date de début de production est obligatoire</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_fabrication" class="form-label required-field">Fin de Production</label>
                            <input type="datetime-local" id="End_Fab_date" name="End_Fab_date"
                                   class="form-control @error('End_Fab_date') is-invalid @enderror"
                                   value="{{ old('End_Fab_date', $fabOrder->End_Fab_date ? \Carbon\Carbon::parse($fabOrder->End_Fab_date)->format('Y-m-d\TH:i') : '') }}"
                                   required>
                            @error('date_fabrication')
                            <div class="invalid-feedback">La date de Fin de production est obligatoire</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Lot_Jus" class="form-label required-field">Lot de Jus</label>
                            <input type="text" id="Lot_Jus" name="Lot_Jus"
                                   class="form-control @error('Lot_Jus') is-invalid @enderror"
                                   value="{{ old('Lot_Jus', $fabOrder->Lot_Jus) }}"
                                   placeholder="Entrez le numéro du lot"
                                   required>
                            @error('Lot_Jus')
                            <div class="invalid-feedback">Le champ Lot de Jus est obligatoire</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="effectif_Reel" class="form-label">Effectif Réel</label>
                            <input type="number" id="effectif_Reel" name="effectif_Reel" class="form-control"
                                   placeholder="Nombre d'employés"
                                   min="0">
                        </div>
                    </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Pf_Qty" class="form-label">Quantité PF</label>
                                    <div class="quantity-input">
                                        <input type="number" id="Pf_Qty" name="Pf_Qty" class="form-control"
                                               placeholder="Quantité PF"
                                            {{ empty($fabOrder->Pf_Qty) || $fabOrder->Pf_Qty == 0 ? 'disabled' : '' }}  required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Set_qty" class="form-label">Quantité Set</label>
                                    <div class="quantity-input">
                                        <input type="number" id="Set_qty" name="Set_qty" class="form-control"

                                               placeholder="Quantité Set"
                                               {{ empty($fabOrder->Set_qty) || $fabOrder->Set_qty == 0 ? 'disabled' : '' }} required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="Tester_qty" class="form-label">Quantité Tester</label>
                                    <div class="quantity-input">
                                        <input type="number" id="Tester_qty" name="Tester_qty" class="form-control"
                                               placeholder="Quantité Testeur"
                                            {{ empty($fabOrder->Tester_qty) || $fabOrder->Tester_qty == 0 ? 'disabled' : '' }} required>
                                    </div>
                                </div>
                            </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="Comment_chaine" class="form-label">Commentaires de la Ligne de Production</label>
                            <textarea id="Comment_chaine" name="Comment_chaine" class="form-control" rows="4"
                                      placeholder="Entrez vos commentaires ou notes concernant la production...">{{ old('Comment_chaine', $fabOrder->Comment_chaine) }}</textarea>
                        </div>
                            </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Form validation messages in French
                const validationMessages = {
                    required: 'Ce champ est obligatoire',
                    number: {
                        min: 'La valeur doit être supérieure ou égale à 0',
                        invalid: 'Veuillez entrer un nombre valide'
                    }
                };

                // Form validation
                const form = document.getElementById('fabricationForm');
                form.addEventListener('submit', function(event) {
                    let isValid = true;
                    const requiredFields = form.querySelectorAll('[required]');

                    requiredFields.forEach(field => {
                        if (!field.value) {
                            isValid = false;
                            field.classList.add('is-invalid');

                            // Add custom validation message
                            let feedback = field.nextElementSibling;
                            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                                feedback = document.createElement('div');
                                feedback.classList.add('invalid-feedback');
                                field.parentNode.appendChild(feedback);
                            }
                            feedback.textContent = validationMessages.required;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        event.preventDefault();
                        alert('Veuillez remplir tous les champs obligatoires');
                    }
                });

                // Real-time validation for number inputs
                const inputs = form.querySelectorAll('input[type="number"]');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        if (this.value < 0) {
                            this.value = 0;
                            let feedback = this.nextElementSibling;
                            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                                feedback = document.createElement('div');
                                feedback.classList.add('invalid-feedback');
                                this.parentNode.appendChild(feedback);
                            }
                            feedback.textContent = validationMessages.number.min;
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

