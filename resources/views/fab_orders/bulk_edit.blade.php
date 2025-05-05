@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <h4>Édition Multiple des Ordres de Fabrication</h4>
        <form action="{{ route('fab_orders.bulk_update') }}" method="POST">
            @csrf
            @method('PUT')

            <div id="orderFormsContainer">
                @foreach($fabOrders as $order)
                    <div class="card mb-4 p-3 border border-primary" id="form_{{ $order->id }}">
                        <h5 class="mb-3">OF #{{ $order->OFID }}</h5>
                        <input type="hidden" name="order_ids[]" value="{{ $order->id }}">

                        <div class="row gy-3">
                            <div class="col-md-3">
                                <label>Produit:</label>
                                <select name="Prod_ID_{{ $order->id }}" class="form-control select2" required>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $product->id == $order->Prod_ID ? 'selected' : '' }}>
                                            {{ $product->ref_id }} - {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Chaine:</label>
                                <select name="chaineID_{{ $order->id }}" class="form-control select2" required>
                                    @foreach ($chaines as $chaine)
                                        <option value="{{ $chaine->id }}" {{ $chaine->id == $order->chaineID ? 'selected' : '' }}>
                                            {{ $chaine->Num_chaine }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Statut:</label>
                                <select name="Statut_of_{{ $order->id }}" class="form-control" required>
                                    <option value="Planifié" {{ $order->Statut_of == 'Planifié' ? 'selected' : '' }}>Planifié</option>
                                    <option value="En cours" {{ $order->Statut_of == 'En cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="Réalisé" {{ $order->Statut_of == 'Réalisé' ? 'selected' : '' }}>Réalisé</option>
                                    <option value="Suspendu" {{ $order->Statut_of == 'Suspendu' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>Date de fabrication:</label>
                                <input type="datetime-local" name="date_fabrication_{{ $order->id }}" class="form-control"
                                       value="{{ old('date_fabrication', $order->date_fabrication ? \Carbon\Carbon::parse($order->date_fabrication)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-success">Mettre à Jour</button>
            <a href="{{ route('fab_orders.index') }}" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Sélectionner",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
