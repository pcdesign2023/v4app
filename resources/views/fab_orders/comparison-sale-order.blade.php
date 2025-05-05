@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Reporting commande</h4>
            </div>
        </div>
    </div>
@endsection
@section('content')

        <div class="row row-sm">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pb-0">

                    </div>
         <div class="card-body">
             Note :(<span class="text-primary font-weight-bold">Déclarée</span> / Plannifiée) </br>
        <div class="table-responsive">
            <table class="table table-bordered" id="comparisonTable">
                <thead>
                <tr>
                    <th>
                        <select id="clientColumnFilter" class="form-control form-control-sm">
                            <option value="">Tous les clients</option>
                        </select>
                    </th>
                    <th>Commande</th>
                    <th>Qté PF</th>
                    <th>Qté Set</th>
                    <th>Qté Tester</th>
                    <th>Taux de réalisation %</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($comparisons as $comparison)
                    @php
                        // Total fab_orders quantity
                        $totalFabOrdersQty = ($comparison->fab_orders_Pf_Qty ?? 0) +
                                             ($comparison->fab_orders_Set_qty ?? 0) +
                                             ($comparison->fab_orders_Tester_qty ?? 0);

                        // Total fabrication quantity
                        $totalFabricationQty = ($comparison->fabrication_Pf_Qty ?? 0) +
                                               ($comparison->fabrication_Set_qty ?? 0) +
                                               ($comparison->fabrication_Tester_qty ?? 0);

                        // Calculate completion percentage (avoid division by zero)
                        $totalPercentage = ($totalFabOrdersQty > 0) ?
                            round(($totalFabricationQty / $totalFabOrdersQty) * 100, 2) : 0;

                        // Determine color class based on completion percentage
                        $percentColor = 'danger'; // Default (red)

                        if ($totalPercentage > 80) {
                            $percentColor = 'success'; // Green
                        } elseif ($totalPercentage > 50) {
                            $percentColor = 'warning'; // Orange
                        }
                    @endphp
                    <tr>
                        <td>{{ $comparison->client_name }}</td>
                        <td>{{ $comparison->saleOrderId }}</td>
                        <td>
                            <span class="text-primary font-weight-bold">{{ $comparison->fabrication_Pf_Qty ?? 0 }}</span> /
                            {{ $comparison->fab_orders_Pf_Qty ?? 0 }}
                        </td>
                       
                        <td>
                            <span class="text-primary font-weight-bold">{{ $comparison->fabrication_Set_qty ?? 0 }}</span> /
                            {{ $comparison->fab_orders_Set_qty ?? 0 }}
                        </td>
                        <td>
                            <span class="text-primary font-weight-bold">{{ $comparison->fabrication_Tester_qty ?? 0 }}</span> /
                            {{ $comparison->fab_orders_Tester_qty ?? 0 }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $percentColor }}">{{ $totalPercentage }}%</span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
                </div>
            </div>
            </div>
        </div>
@endsection
@section('js')
    <!-- jQuery (Required for DataTables) -->
    <script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#comparisonTable').DataTable({
                pageLength: 100,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json',
                    searchPlaceholder: "Rechercher...",
                    search: "Rechercher:",
                    lengthMenu: "Afficher _MENU_ entrées",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    infoEmpty: "Aucune donnée disponible",
                    infoFiltered: "(filtré de _MAX_ entrées au total)",
                    paginate: {
                        first: "Premier",
                        last: "Dernier",
                        next: "Suivant",
                        previous: "Précédent"
                    }
                },
                initComplete: function () {
                    let api = this.api();
                    let column = api.column(0); // Column index for Client
                    let select = $('#clientColumnFilter');
                    let uniqueClients = [];

                    // Get unique values
                    column.data().each(function (d) {
                        let clientName = $('<div>').html(d).text().trim();
                        if (clientName && !uniqueClients.includes(clientName)) {
                            uniqueClients.push(clientName);
                        }
                    });

                    uniqueClients.sort().forEach(function (client) {
                        select.append(`<option value="${client}">${client}</option>`);
                    });

                    // On select change, filter column
                    select.on('change', function () {
                        let val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                }
            })
            $('.edit-fabrication').on('click', function () {
                // Get data from the clicked button
                var fabricationId = $(this).data('id');
                var ofid = $(this).data('saleOrderId') || 'NAN';
                var lotjus = $(this).data('lotjus') || 'NAN';
                var validdate = $(this).data('validdate') || 'NAN';
                var effectif = $(this).data('effectif') || 'NAN';
                var datefabrication = $(this).data('datefabrication') || 'NAN';
                var pfqty = $(this).data('pfqty') || '0';
                var sfqty = $(this).data('sfqty') || '0';
                var setqty = $(this).data('setqty') || '0';
                var testerqty = $(this).data('testerqty') || '0';
                var comment = $(this).data('comment') || 'NAN';
                console.log(ofid);

                // Pre-fill the form inputs with retrieved data
                $('#editFabricationId').val(fabricationId);
                $('#editOFID').val(saleOrderId);
                $('#editLotJus').val(lotjus);
                $('#editValidDate').val(validdate);
                $('#editEffectifReel').val(effectif);
                $('#editDateFabrication').val(datefabrication);
                $('#editPfQty').val(pfqty);
                $('#editSfQty').val(sfqty);
                $('#editSetQty').val(setqty);
                $('#editTesterQty').val(testerqty);
                $('#editComment').val(comment);

                // Show the modal
                $('#editFabricationModal').modal('show');
            });
        });

    </script>
@endsection
