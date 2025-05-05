@extends('layouts.master')
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">ORDRES DE FABRICATION</h4>

            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('fab_orders.create') }}" class="btn btn-success btn-sm">
                <i class="mdi mdi-plus"></i> Nouveau OF
            </a>
            <a href="#" class="btn btn-info btn-sm ml-2" data-toggle="modal" data-target="#importExcelModal">
    <i class="mdi mdi-file-excel"></i> Import Excel
</a>

        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- row opened -->
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


    <div class="table-responsive">

                        <table class="table text-md-nowrap" id="fabOrdersTable">
                            <thead>
                            <tr>
                                <th>
                                    <select id="clientColumnFilter" class="form-control form-control-sm">
                                        <option value="">Tous les clients</option>
                                    </select>
                                </th>                       
                                <th style="width:10px !important;">Commande</th>
                                <th>OF</th>
                                <th>Produit</th>
                                <th>Chaine</th>
                                <th> Date planification </th>
                                <th>PF Qte</th>
                                <!--<th>SF Qte</th>-->
                                <th>Tester Qte</th>
                                <th>SET Qte</th>
                                <th>Total Qte</th>

                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($fabOrders as $index => $fabOrder)
                                <tr>
                                    <td>{{ $fabOrder->client->name ?? 'N/A' }}</td>
                                    <td>{{ $fabOrder->saleOrderId }}</td>
                                    <td>{{ $fabOrder->OFID }}</td>
                                    <td>   <a href="#"
                                              class="view-bom-btn"
                                              data-id="{{  $fabOrder->product->ref_id }}"
                                              data-name="{{  $fabOrder->product->product_name }}">
                                            {{ $fabOrder->product->product_name ?? 'N/A' }}
                                        </a>

                                        <span class="badge badge-success">
                                                {{ $fabOrder->product->ref_id ?? 'N/A' }}
                                            </span></td>
                                    <td>{{ $fabOrder->chaine->Num_chaine ?? 'N/A' }}</td>
                                    <td style="width: 1px !important;">{{ $fabOrder->date_fabrication ? \Carbon\Carbon::parse($fabOrder->date_fabrication)->locale('fr')->translatedFormat('d M ') : 'Non fourni' }}</td>

                                    <td>{{ $fabOrder->Pf_Qty }}</td>
                                    <!--<td>{{ $fabOrder->Sf_Qty }}</td> -->
                                    <td>{{ $fabOrder->Tester_qty }}</td>
                                    <td>{{ $fabOrder->Set_qty }}</td>
                                    <td class="total-qte">0</td>

                                    <td>
                                        @php
                                            $badgeClass = 'secondary'; // Default class if no conditions match

                                            if ($fabOrder->Statut_of == 'Réalisé') {
                                                $badgeClass = 'success';
                                            } elseif ($fabOrder->Statut_of == 'Planifié') {
                                                $badgeClass = 'primary';
                                            } elseif ($fabOrder->Statut_of == 'En cours' ) {
                                                $badgeClass = 'warning';
                                            } elseif ($fabOrder->Statut_of == 'Suspendu') {
                                                $badgeClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }}">
    {{ $fabOrder->Statut_of }}
</span>

                                    </td>
                                    <td>
                                        <a href="{{ route('fab_orders.edit', $fabOrder->id) }}" class="btn btn-sm btn-primary">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <!--<button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#commentModal-{{ $fabOrder->id }}">
                                            <i class="mdi mdi-comment"></i>
                                        </button>-->
                                        <form action="{{ route('fab_orders.destroy', $fabOrder->id) }}" method="POST"
                                              style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure?')">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                        <button  style="display:inline-block;"  class="btn btn-info btn-sm view-history"
                                                 data-ofid="{{ $fabOrder->OFID }}"
                                                 data-pfqtyfab="{{ $fabOrder->Pf_Qty }}"
                                                 data-sfqtyfab="{{ $fabOrder->Sf_Qty }}"
                                                 data-testerqtyfab="{{ $fabOrder->Tester_qty }}"
                                                 data-setqtyfab="{{ $fabOrder->Set_qty ?? '0' }}"
                                                 data-toggle="modal"
                                                 data-target="#historyModal">
                                            <i class="mdi mdi-history"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal for Comments -->
                                <div class="modal fade" id="commentModal-{{ $fabOrder->id }}" tabindex="-1" role="dialog"
                                     aria-labelledby="commentModalLabel-{{ $fabOrder->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="commentModalLabel-{{ $fabOrder->id }}">Comment</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <label for="Comment_OF"><strong>Instructions :</strong></label>
                                                <p>{{ $fabOrder->instruction }}</p>
                                                <hr>
                                                <label for="Comment_OF"><strong>Commentaire   :</strong></label>
                                                <p>{{ $fabOrder->Comment_chaine ?? 'Not Provided' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Modal -->
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- Import Excel Modal -->
<div class="modal fade" id="importExcelModal" tabindex="-1" role="dialog" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('fab_orders.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-info text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title mb-0" id="importExcelModalLabel">Importer les OF depuis un fichier Excel</h5>
                        <h6>
                            <a href="{{ asset('templates/template_of_import.xlsx') }}" class="text-light" download>
                                📄 Télécharger le modèle Excel
                            </a>
                        </h6>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="excel_file" class="font-weight-bold">Fichier Excel</label>
                        <div class="custom-file">
                            <input type="file" name="excel_file" class="custom-file-input" id="excel_file" accept=".xls,.xlsx" required>
                            <label class="custom-file-label" for="excel_file">Choisir un fichier...</label>
                        </div>
                        <small class="form-text text-muted mt-1">Formats acceptés : .xls, .xlsx</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">📤 Importer</button>
                </div>
            </div>
        </form>
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
                    <div class="text-center mb-3">
                        <img id="product-image" src="" alt="Product Image" class="img-fluid" style="max-height: 200px;">
                    </div>
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
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"></script>
    <script>
        $(document).ready(function () {
            var table =  $('#fabOrdersTable').DataTable({
                destroy: true, 
                pageLength: 100,
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                },
                initComplete: function() {
                    // Populate Client Dropdown in header
                    let column = this.api().column(0);
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
                     // 🔥 Filter DataTable Based on Selected Chaine ID
                    $('#userFilter').on('change', function () {
                        let selectedChaineId = $(this).val(); // Get selected Chaine ID

                        table.column(4) // Adjust this index to match the 'chaine' column
                            .search(selectedChaineId) // Apply search filter
                            .draw(); // Redraw the table
                    });
                }
            });
            // Calculate Total Qte for each row
$('#fabOrdersTable tbody tr').each(function () {
    let pf = parseFloat($(this).find('td').eq(6).text()) || 0;
    let sf = parseFloat($(this).find('td').eq(7).text()) || 0;
    let tester = parseFloat($(this).find('td').eq(8).text()) || 0;
    let set = parseFloat($(this).find('td').eq(9).text()) || 0;
    let total = pf + sf + tester + set;

    $(this).find('.total-qte').text(total);
});
// Status filter buttons (multiple toggle logic)
            $(document).on('change', '.status-checkbox', function () {
                let selectedStatuses = $('.status-checkbox:checked')
                    .map(function () {
                        return $(this).val();
                    }).get();

                if (selectedStatuses.length === 0) {
                    table.column(11).search('').draw(); // Show all if none selected
                } else {
                    let regex = selectedStatuses.join('|');
                    table.column(11).search(regex, true, false).draw();
                }
            });


        });

    </script>
    <script>
        $(document).ready(function() {
            $('.view-bom-btn').click(function() {
                const productId = $(this).data('id');
                const productName = $(this).data('name');

                // Set Modal Title
                $('#bomModalLabel').text(`BOM Materials for ${productName}`);

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
                            if (response.length > 0 && response[0].image_url) {
                                const fullImageUrl = `https://pcdesign-staging-new-19171724.dev.odoo.com${response[0].image_url}`;
                                $('#product-image').attr('src', fullImageUrl).show();
                            }
                            response.forEach(material => {
                                rows += `
                                <tr>
                                    <td>${material.component_name ?? 'N/A'}</td>
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
            $('.view-history').on('click', function () {
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
                    url: '/fabrication/history/' + btoa(ofid), // Base64 encode OFID
                    method: 'GET',

                    success: function (response) {
                        var tableBody = $('#history-table-body');
                        tableBody.empty();

                        if (response.length > 0) {
                            var totalPfQty = 0; // Initialize total for Pf_Qty
                            var totalSfQty = 0; // Initialize total for Sf_Qty
                            var totalSetQty = 0; // Initialize total for Set_qty
                            var totalTesterQty = 0; // Initialize total for Tester_qty
                            response.forEach(function (record) {
                                // Calculate totals for each quantity
                                totalPfQty += record.Pf_Qty ? parseFloat(record.Pf_Qty) : 0;
                                totalSfQty += record.Sf_Qty ? parseFloat(record.Sf_Qty) : 0;
                                totalSetQty += record.Set_qty ? parseFloat(record.Set_qty) : 0;
                                totalTesterQty += record.Tester_qty ? parseFloat(record.Tester_qty) : 0;
                                var start = new Date(record.date_fabrication);
                                var end = new Date(record.End_Fab_date);


                                // Calculate the difference in milliseconds
                                var diffMs = end - start;

                                // Convert milliseconds to hours
                                var totalMinutes = Math.floor(diffMs / (1000 * 60));
                                // Extract hours and minutes
                                var hours = Math.floor(totalMinutes / 60); // Get full hours
                                var minutes = totalMinutes % 60; // Get remaining minutes

                                tableBody.append(`
                                <tr>
                                    <td>${record.OFID ?? 'NAN'}</td>
                                    <td>${record.Lot_Jus ?? 'NAN'}</td>
                                    <td>${record.Valid_date ?? 'NAN'}</td>
                                    <td>${record.effectif_Reel ?? 'NAN'}</td>
                                    <td>${record.date_fabrication ? new Intl.DateTimeFormat('fr-FR', {
                                    day: 'numeric',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    hour12: false
                                }).format(new Date(record.date_fabrication)).replace(',', '') : 'NAN'}</td>
                                    <td>${record.Pf_Qty ?? 'NAN'}</td>
                                    <td>${record.Sf_Qty ?? 'NAN'}</td>
                                    <td>${record.Set_qty ?? 'NAN'}</td>
                                    <td>${record.Tester_qty ?? 'NAN'}</td>
                                    <td>${record.Comment_chaine ?? 'NAN'}</td>
                                    <td>${hours}h ${minutes}m</td>
                                </tr>
                            `);
                            });

                            // Append the totals to their respective spans
                            $('#PF').text(totalPfQty.toFixed(0)); // Total Pf_Qty
                            $('#SF').text(totalSfQty.toFixed(0)); // Total Sf_Qty
                            $('#SET').text(totalSetQty.toFixed(0)); // Total Set_qty
                            $('#TESTER').text(totalTesterQty.toFixed(0)); // Total Tester_qty
                        } else {
                            tableBody.append('<tr><td colspan="10">No history found for this order.</td></tr>');
                            // Reset all totals to 0 if no data is found
                            $('#PF').text('0.00');
                            $('#SF').text('0.00');
                            $('#SET').text('0.00');
                            $('#TESTER').text('0.00');
                        }
                    },
                    error: function () {
                        $('#history-table-body').html('<tr><td colspan="10">Error loading history.</td></tr>');
                        // Reset all totals to 0 on error
                        $('#PF').text('0.00');
                        $('#SF').text('0.00');
                        $('#SET').text('0.00');
                        $('#TESTER').text('0.00');
                    }
                });
            });
        });
    </script>
    
@endsection
