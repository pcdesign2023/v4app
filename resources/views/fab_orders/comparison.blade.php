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
                <h4 class="content-title mb-0 my-auto">Reporting de Fabrication</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0"></div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="card shadow-sm" style="min-width: 70%;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <!-- ✅ Status Filters -->
                                        <div class="col-md-8 mb-2 mb-md-0">
                                            <div class="d-flex flex-wrap align-items-center">
                                                <span class="font-weight-bold mr-3">📌 Statut :</span>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-checkbox" type="checkbox" value="Planifié" id="statusPlanifie">
                                                    <label class="form-check-label" for="statusPlanifie">Planifié</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-checkbox" type="checkbox" value="En cours" id="statusEnCours">
                                                    <label class="form-check-label" for="statusEnCours">En cours</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-checkbox" type="checkbox" value="Réalisé" id="statusRealise">
                                                    <label class="form-check-label" for="statusRealise">Réalisé</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input status-checkbox" type="checkbox" value="Suspendu" id="statusSuspendu">
                                                    <label class="form-check-label" for="statusSuspendu">Suspendu</label>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                                 Note :(<span class="text-primary font-weight-bold">Déclarée</span> / Plannifiée) </br>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="comparisonTable">
                            <thead>
                                <tr>
<th>
                        <select id="clientColumnFilter" class="form-control form-control-sm">
                            <option value="">Tous les clients</option>
                        </select>
                    </th>                                    <th>Commande</th>
                                    <th>N° OF</th>
                                    <th>Qté PF </th>
                                    <th>Qté Set </th>
                                    <th>Qté Testeur</th>
                                    <th>Taux de Réalisation %</th>
                                    <th>Status</th>
                                    <th>Clôture</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comparisons as $comparison)
                                    @php
                                        $totalFabOrdersQty = ($comparison->fab_orders_Pf_Qty ?? 0) + 
                                                             ($comparison->fab_orders_Set_qty ?? 0) + 
                                                             ($comparison->fab_orders_Tester_qty ?? 0);

                                        $totalFabricationQty = ($comparison->fabrication_Pf_Qty ?? 0) + 
                                                               ($comparison->fabrication_Set_qty ?? 0) + 
                                                               ($comparison->fabrication_Tester_qty ?? 0);

                                        $calc = $totalFabricationQty / ($totalFabOrdersQty ?: 1); 
                                        $totalPercentage = round($calc * 100, 2);

                                        $percentColor = 'danger';
                                        if ($totalPercentage > 80) $percentColor = 'success';
                                        elseif ($totalPercentage > 50) $percentColor = 'warning';
                                    @endphp
                                    <tr>
                                        <td>{{ $comparison->client_name }}</td>
                                        <td>{{ $comparison->saleOrderId }}</td>
                                        <td>{{ $comparison->OFID }}</td>
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
                                        <td>

    <span>{{ $comparison->statut_of}}
</span>
</span>

                                        </td>
                                        <td>
                                            @if ($comparison->statut_of === 'Réalisé')
                                                <span class="text-success font-weight-bold">✅</span>
                                            @else
                                                <form action="{{ route('faborder.updateStatus', ['OFID' => urlencode($comparison->OFID)]) }}" method="POST" class="d-inline update-status-form">
                                                    @csrf
                                                    <button type="button"
                                                            class="btn btn-sm btn-primary update-status-btn"
                                                            data-ofid="{{ urlencode($comparison->OFID) }}">
                                                        clôturer
                                                    </button>
                                                </form>
                                            @endif
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            var table =  $('#comparisonTable').DataTable({
                responsive: true,
                pageLength: 100,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
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

            });
            // Status filter buttons (multiple toggle logic)
            $(document).on('change', '.status-checkbox', function () {
                let selectedStatuses = $('.status-checkbox:checked')
                    .map(function () {
                        return $(this).val();
                    }).get();

                if (selectedStatuses.length === 0) {
                    table.column(7).search('').draw(); // Show all if none selected
                } else {
                    let regex = selectedStatuses.join('|');
                    table.column(7).search(regex, true, false).draw();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.update-status-btn').click(function () {
                var button = $(this); // Get button reference
                var OFID = button.data('ofid');
                var encodedOFID = encodeURIComponent(OFID); // Encode for URL

                $.ajax({
                    url: "/faborder/update-status/" + encodedOFID, // Use encoded OFID
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        if (response.success) {
                            // Replace the entire parent <td> content with ✅
                            let td = button.closest('td');
                            td.html('<span class="text-success font-weight-bold">✅</span>');
                        }
                    },
                    error: function (xhr) {
                        alert('Error updating status. Please try again.');
                    }
                });
            });
        });
    </script>
@endsection
