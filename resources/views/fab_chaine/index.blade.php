@extends('layouts.master')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-role" content="{{ auth()->user()->role->label }}">

</head>
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"/>
    <style>
        /* Stylish Modal Cards */
        .card .badge {
            font-size: 1.25rem;
            font-weight: bold;
        }

        /* Sticky Header for Table */
        .table thead.sticky-top th {
            position: sticky;
            top: 0;
            z-index: 2;
        }

        /* Hover Effect for Rows */
        .table-hover tbody tr:hover {
            background-color: #f0f8ff;
        }

        /* Zebra Striping for Table */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Elegant Modal Header */
        .modal-header {
            font-family: 'Poppins', sans-serif;
        }

    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
     
        @endsection

        @section('content')
            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="d-flex justify-content-center mb-4">
                                <div class="card shadow-sm" style="min-width: 70%;">
                                    <div class="card-body">
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

                                            <!-- ✅ Chef de Chaine Filter (inline) -->
                                            <div class="col-md-4 d-flex align-items-center justify-content-end">
                                                <label for="userFilter" class="font-weight-bold mb-0 mr-2">👤 Chef de Chaine :</label>
                                                <select id="userFilter" class="form-control form-control-sm w-auto">
                                                    <option value="">Tous</option>
                                                    @foreach ($chaines as $chaine)
                                                        @if ($chaine->chef)
                                                            <option value="{{ $chaine->Num_chaine }}">{{ $chaine->chef->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive p-4">

                            <table class="table table-striped mg-b-0 text-md-nowrap" id="planningTable">

                                <thead>
                                <tr>
                                    <th>DATE PLANIFIÉE</th>
                                    <th>OF ID</th>
                                    <th style="display:s;">chaine</th>
                                    <th>
                                        <select id="clientColumnFilter" class="form-control form-control-sm">
                                            <option value="">Tous les clients</option>
                                        </select>
                                    </th>
                                    <th>Commande</th>
                                    <th>Produit</th>
                                    <th>PF</th>
                                    <th>Tester</th>
                                    <th>Set</th>
                                    <th>Total Qte</th>

                                    <th>+</th>
                                    <th>hist</th>

                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($fabOrders as $order)
                                    <tr>
                                        <td>{{ $order->date_fabrication ? \Carbon\Carbon::parse($order->date_fabrication)->locale('fr')->translatedFormat('d M ') : 'Non fourni' }}</td>

                                        <td>{{ $order->OFID }}</br>
                                            @php
                                                $badgeClass = 'secondary'; // Default class if no conditions match

                                                if ($order->Statut_of == 'Réalisé') {
                                                    $badgeClass = 'success';
                                                } elseif ($order->Statut_of == 'Planifié') {
                                                    $badgeClass = 'primary';
                                                } elseif ($order->Statut_of == 'En cours' ) {
                                                    $badgeClass = 'warning';
                                                } elseif ($order->Statut_of == 'Suspendu') {
                                                    $badgeClass = 'danger';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
    {{ $order->Statut_of }}
</span>
                                        </td>
                                        <td style="display:22;">{{ $order->chaine->Num_chaine  }}</td>
                                        <td>{{ $order->client->name ?? 'N/A' }}</td>
                                        <td>{{ $order->saleOrderId }}</td>
                                        <td>
                                            <a href="#" class="view-bom-btn"
                                               data-id="{{  $order->product->ref_id }}"
                                               data-name="{{  $order->product->product_name }}">
                                                {{ $order->product->product_name ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td>{{ $order->Pf_Qty ?? '0' }}</td>
                                        <td>{{ $order->Tester_qty ?? '0' }}</td>
                                        <td>{{ $order->Set_qty ?? '0' }}</td>
                                        <td class="total-qte">0</td>
                                        <td>
                                            <a href="{{ route('fab_chain.edit', $order->id) }}"
                                               class="btn btn-success btn-sm">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>

                                        </td>
                                        <td>

                                            <button  style="display:inline-block;"  class="btn btn-info btn-sm view-history"
                                                     data-ofid="{{ $order->OFID }}"
                                                     data-pfqtyfab="{{ $order->Pf_Qty }}"
                                                     data-sfqtyfab="{{ $order->Sf_Qty }}"
                                                     data-testerqtyfab="{{ $order->Tester_qty }}"
                                                     data-setqtyfab="{{ $order->Set_qty ?? '0' }}"
                                                     data-toggle="modal"
                                                     data-target="#historyModal">
                                                <i class="mdi mdi-history"></i>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div><!-- bd -->
                    </div><!-- bd -->
                </div><!-- bd -->
            </div>
    </div>
    {{-- BOM Modal --}}
    <div class="modal fade" id="bomModal" tabindex="-1" role="dialog" aria-labelledby="bomModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bomModalLabel">BOM Materials</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Composant</th>
                            <th>Reference</th>
                            <th>Quantité</th>
                        </tr>
                        </thead>
                        <tbody id="bomTableBody">
                        <tr>
                            <td colspan="3" class="text-center">No data available</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Redesigned Fabrication History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content shadow-lg rounded">
                <!-- Modal Header -->
                <div class="modal-header  text-bg-dark">
                    <h5 class="modal-title font-weight-bold">📜 Historique de Fabrication Ref: <span id="modalOFID" class="text-warning"></span></h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Totals Section with Stylish Cards -->
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="text-uppercase font-weight-bold">PF</h6>
                                    <span id="PF" class="badge badge-primary" style="font-size: 0.8rem !important;">0.00</span> <bold>/</bold>
                                    <span id="pfqtyfab" class="success" style="font-size: 0.8rem !important;">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-uppercase font-weight-bold">SF</h6>
                                    <span id="SF" class="badge badge-success" style="font-size: 0.8rem !important;">0.00</span><bold>/</bold>
                                    <span id="sfqtyfab" class="success" style="font-weight: 900;font-size: 0.8rem !important;">0.00</span>

                                </div>
                            </div>

                        </div>
                        <div class="col-md-3">
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h6 class="text-uppercase font-weight-bold">SET</h6>
                                    <span id="SET" class="badge badge-danger" style="font-size: 0.8rem !important;">0.00</span><bold>/</bold>
                                    <span id="setqtyfab" class="success" style="font-weight: 900;font-size: 0.8rem !important;">0.00</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h6 class="text-uppercase font-weight-bold">TESTER</h6>
                                    <span id="TESTER" class="badge badge-warning" style="font-size: 0.8rem !important;">0.00</span><bold>/</bold>
                                    <span id="testerqtyfab" class="success" style="font-weight: 900;font-size: 0.8rem !important;">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History Table with Sticky Header & Hover Effect -->
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table id="history-table" class="table table-bordered table-hover">
                            <thead class="bg-dark text-white sticky-top">
                            <tr>
                                <th>OFID</th>
                                <th>LOT JUS</th>
                                <th>VALID DATE</th>
                                <th>EFFECTIF REEL</th>
                                <th>FABRICATION DATE</th>
                                <th>PF QTY</th>
                                <th>SF QTY</th>
                                <th>SET QTY</th>
                                <th>TESTER QTY</th>
                                <th>COMMENT</th>
                                <th>spent</th>

                                @if(auth()->user()->role->label === 'Administrateur')
                                    <th>Action</th> <!-- Show Action column only for Administrateurs -->
                                @endif
                            </tr>
                            </thead>
                            <tbody id="history-table-body" class="table-striped">
                            <tr>
                                <td colspan="10" class="text-center">Loading...</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal Footer -->
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary" data-dismiss="modal"> Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Fabrication History Modal -->
    <div class="modal fade" id="editHistoryModal" tabindex="-1" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editFabricationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title text-white">✏️ Modifier l'enregistrement</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="fab_id" id="editFabId">
                        <div class="form-group">
                            <label>LOT JUS</label>
                            <input type="text" name="Lot_Jus" class="form-control" id="editLot">
                        </div>
                        <div class="form-group">
                            <label>Valid Date</label>
                            <input type="date" name="Valid_date" class="form-control" id="editValid">
                        </div>
                        <div class="form-group">
                            <label>Effectif Réel</label>
                            <input type="text" name="effectif_Reel" class="form-control" id="editEffectif">
                        </div>
                        <div class="form-group">
                            <label>Commentaire</label>
                            <textarea name="Comment_chaine" class="form-control" id="editComment"></textarea>
                        </div>
                        <div class="form-group">
                            <label>PF QTY</label>
                            <input type="number" name="Pf_Qty" class="form-control" id="editPf">
                        </div>
                        <div class="form-group">
                            <label>SF QTY</label>
                            <input type="number" name="Sf_Qty" class="form-control" id="editSf">
                        </div>
                        <div class="form-group">
                            <label>SET QTY</label>
                            <input type="number" name="Set_qty" class="form-control" id="editSet">
                        </div>
                        <div class="form-group">
                            <label>TESTER QTY</label>
                            <input type="number" name="Tester_qty" class="form-control" id="editTester">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('js')
    <!--<script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script> -->

    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"></script>

    <script>
        $(document).ready(function () {
            var table = $('#planningTable').DataTable({
                destroy: true,
                responsive: true,
                autoWidth: false,
                paging: true,
                pageLength: 100,
                ordering: true,
                info: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                },
                initComplete: function() {
                    // Populate Client Dropdown in header
                    let column = this.api().column(3);
                    let select = $('#clientColumnFilter');
                    let uniqueClients = [];

                    column.data().each(function (d) {
                        let val = $('<div>').html(d).text().trim();
                        if (val && !uniqueClients.includes(val)) {
                            uniqueClients.push(val);
                        }
                    });

                    uniqueClients.sort().forEach(function (client) {
                        select.append(`<option value="${client}">${client}</option>`);
                    });

                    // Filter on select change
                    select.on('change', function () {
                        let val = $.fn.dataTable.util.escapeRegex($(this).val());
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });
                    // Calculate Total Qte for each row
                    $('#planningTable tbody tr').each(function () {
                        let pf = parseFloat($(this).find('td').eq(6).text()) || 0;
                        let tester = parseFloat($(this).find('td').eq(7).text()) || 0;
                        let setqte = parseFloat($(this).find('td').eq(8).text()) || 0;
                        let total = pf + tester + setqte;

                        $(this).find('.total-qte').text(total);
                    });
                    // Status filter buttons (multiple toggle logic)
                    $(document).on('change', '.status-checkbox', function () {
                        let selectedStatuses = $('.status-checkbox:checked')
                            .map(function () {
                                return $(this).val();
                            }).get();

                        if (selectedStatuses.length === 0) {
                            table.column(1).search('').draw(); // Show all if none selected
                        } else {
                            let regex = selectedStatuses.join('|');
                            table.column(1).search(regex, true, false).draw();
                        }
                    });
                }
            });
            // 🔥 Filter DataTable Based on Selected Chaine ID
            $('#userFilter').on('change', function () {
                let selectedChaineId = $(this).val(); // Get selected Chaine ID

                table.column(2) // Adjust this index to match the 'chaine' column
                    .search(selectedChaineId) // Apply search filter
                    .draw(); // Redraw the table
            });
            $(document).on('click', '.edit-fabrication', function () {
                $('#editFabId').val($(this).data('id'));
                $('#editLot').val($(this).data('lot'));
                $('#editValid').val($(this).data('valid'));
                $('#editEffectif').val($(this).data('effectif'));
                $('#editComment').val($(this).data('comment'));
                $('#editPf').val($(this).data('pf'));
                $('#editSf').val($(this).data('sf'));
                $('#editSet').val($(this).data('set'));
                $('#editTester').val($(this).data('tester'));

                // Optional: dynamically set the form action
                let recordId = $(this).data('id');
                $('#editFabricationForm').attr('action', `/fabrication/update/${recordId}`);
            });

            $('.view-history').on('click', function () {
                var userRole = $('meta[name="user-role"]').attr('content'); // Get user role

                var ofid = $(this).data('ofid');
                var pfqtyfab = $(this).data('pfqtyfab');
                var sfqtyfab = $(this).data('sfqtyfab');
                var testerqtyfab = $(this).data('testerqtyfab');
                var setqtyfab = $(this).data('setqtyfab');
                $('#modalOFID').text(ofid);
                $('#pfqtyfab').text(pfqtyfab);
                $('#sfqtyfab').text(sfqtyfab);
                $('#testerqtyfab').text(testerqtyfab);
                $('#setqtyfab').text(setqtyfab);
                $('#history-table-body').html('<tr><td colspan="10">Loading...</td></tr>');

                $.ajax({
                    url: '/fabrication/history/' + btoa(ofid),
                    method: 'GET',

                    success: function (response) {
                        var tableBody = $('#history-table-body');
                        tableBody.empty();

                        if (response.length > 0) {
                            var totalPfQty = 0;
                            var totalSfQty = 0;
                            var totalSetQty = 0;
                            var totalTesterQty = 0;

                            response.forEach(function (record) {
                                totalPfQty += record.Pf_Qty ? parseFloat(record.Pf_Qty) : 0;
                                totalSfQty += record.Sf_Qty ? parseFloat(record.Sf_Qty) : 0;
                                totalSetQty += record.Set_qty ? parseFloat(record.Set_qty) : 0;
                                totalTesterQty += record.Tester_qty ? parseFloat(record.Tester_qty) : 0;

                                var start = new Date(record.date_fabrication);
                                var end = new Date(record.End_Fab_date);
                                var diffMs = end - start;
                                var totalMinutes = Math.floor(diffMs / (1000 * 60));
                                var hours = Math.floor(totalMinutes / 60);
                                var minutes = totalMinutes % 60;

                                // Default blank values
                                var deleteButton = '';
                                var editButton = '';
                                var actionTd = '';

                                if (userRole === "Administrateur") {
                                    deleteButton = `
                            <a href='#' class='btn btn-danger btn-sm delete-fabrication' data-id='${record.id}'>
                                <i class='mdi mdi-delete'></i>
                            </a>`;

                                    editButton = `
                            <button class='btn btn-warning btn-sm edit-fabrication'
                                data-id='${record.id}'
                                data-ofid='${record.OFID}'
                                data-lot='${record.Lot_Jus ?? ''}'
                                data-valid='${record.Valid_date ?? ''}'
                                data-effectif='${record.effectif_Reel ?? ''}'
                                data-datefab='${record.date_fabrication ?? ''}'
                                data-comment='${record.Comment_chaine ?? ''}'
                                data-pf='${record.Pf_Qty ?? 0}'
                                data-sf='${record.Sf_Qty ?? 0}'
                                data-set='${record.Set_qty ?? 0}'
                                data-tester='${record.Tester_qty ?? 0}'
                                data-toggle='modal'
                                data-target='#editHistoryModal'>
                                <i class='mdi mdi-pencil'></i>
                            </button>`;
                                    actionTd = `<td>${deleteButton}${editButton}</td>`;
                                }

                                var rowHtml = `
                        <tr>
                            <td>${record.OFID ?? 'NAN'}</td>
                            <td>${record.Lot_Jus ?? 'NAN'}</td>
                            <td>${record.Valid_date ?? 'NAN'}</td>
                            <td>${record.effectif_Reel ?? 'NAN'}</td>
                            <td>${record.date_fabrication ? new Intl.DateTimeFormat('fr-FR', {
                                    day: 'numeric', month: 'short', year: 'numeric',
                                    hour: 'numeric', minute: 'numeric', hour12: false
                                }).format(new Date(record.date_fabrication)).replace(',', '') : 'NAN'}</td>
                            <td>${record.Pf_Qty ?? 'NAN'}</td>
                            <td>${record.Sf_Qty ?? 'NAN'}</td>
                            <td>${record.Set_qty ?? 'NAN'}</td>
                            <td>${record.Tester_qty ?? 'NAN'}</td>
                            <td>${record.Comment_chaine ?? 'NAN'}</td>
                            <td>${hours}h ${minutes}m</td>
                            ${actionTd}
                        </tr>`;
                                tableBody.append(rowHtml);
                            });

                            $('#PF').text(totalPfQty.toFixed(0));
                            $('#SF').text(totalSfQty.toFixed(0));
                            $('#SET').text(totalSetQty.toFixed(0));
                            $('#TESTER').text(totalTesterQty.toFixed(0));

                        } else {
                            tableBody.append('<tr><td colspan="10">No history found for this order.</td></tr>');
                            $('#PF, #SF, #SET, #TESTER').text('0.00');
                        }
                    },
                    error: function () {
                        $('#history-table-body').html('<tr><td colspan="10">Error loading history.</td></tr>');
                        $('#PF, #SF, #SET, #TESTER').text('0.00');
                    }
                });
            });

// DELETE button handler
            $(document).on('click', '.delete-fabrication', function (e) {
                e.preventDefault();
                var fabricationId = $(this).data('id');

                if (!confirm("Are you sure you want to delete this fabrication record?")) return;

                $.ajax({
                    url: '/fabrication/delete/' + fabricationId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON.message || "An error occurred.");
                    }
                });
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('.view-bom-btn').click(function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');

                // Set Modal Title
                $('#bomModalLabel').text(`Nomenclature  :  ${productName}`);

                // Clear existing BOM table
                $('#bomTableBody').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');

                // Fetch BOM via AJAX
                $.ajax({
                    url: "{{ route('products.bom') }}",
                    type: "GET",
                    data: { ref_id: productId },
                    success: function(response) {
                        if (response.length > 0) {
                            let rows = '';
                            response.forEach(material => {
                                rows += `
                                <tr>
                                    <td style="width: 70%">${material.component_name ?? 'N/A'}</td>
                                    <td>${material.component_code ?? 'N/A'}</td>
                                    <td>${material.Quantity ?? 'N/A'}</td>
                                </tr>`;
                            });
                            $('#bomTableBody').html(rows);
                        } else {
                            $('#bomTableBody').html('<tr><td colspan="3" class="text-center">No materials found</td></tr>');
                        }
                    },
                    error: function() {
                        $('#bomTableBody').html('<tr><td colspan="3" class="text-center text-danger">Failed to load BOM</td></tr>');
                    }
                });

                // Show Modal
                $('#bomModal').modal('show');
            });
        });
    </script>
@endsection
