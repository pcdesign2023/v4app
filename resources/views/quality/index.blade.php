@extends('layouts.master')
<head><meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}">

@endsection
<style>
    .table-fixed {
        table-layout: fixed; /* Ensure the table respects column widths */
        width: 100%; /* Make the table fill its container */
    }
    .table-fixed th,
    .table-fixed td {
        width: 100px; /* Set a fixed width for each column (adjust as needed) */
        overflow: hidden; /* Hide overflowing text */
        white-space: nowrap; /* Prevent text from wrapping to the next line */
        text-overflow: ellipsis; /* Add ellipsis for overflowing text */
        cursor: pointer; /* Change cursor to pointer to indicate clickable cells */
    }

    /* Overlay to display full text */
    .text-overlay {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-width: 80%;
        word-wrap: break-word;
    }
</style>
    <!-- breadcrumb -->
  @section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Déclaration Qualité</h4>
            </div>
        </div>
    </div>
@endsection
    <!-- breadcrumb -->

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                </div>
                <div class="card-body">
                    <div class="mb-3 text-center">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="card shadow-sm" style="min-width: 70%;">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">

                                            <label for="userFilter" class="font-weight-bold mb-0 mr-2">👤 Chef de Chaine :</label>

                                            <select id="userFilter" class="form-control" style="width: auto;">
                                                <option value="">Tous</option>
                                                @foreach ($chaines as $chaine)
                                                    @if ($chaine->chef)
                                                        <option value="{{ $chaine->chef->name  }}">{{ $chaine->chef->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($fabOrders->isEmpty())
            <p>No completed orders found.</p>
        @else

            <table id="qualityTable" class="table table-striped">
                <thead>
                <tr>
                    <th># OF </th>
                     <th>
                        <select id="clientColumnFilter" class="form-control form-control-sm">
                            <option value="">Tous les clients</option>
                        </select>
                    </th>
                    <th>Commande</th>
                    <th  style="display:none">>chef</th>
                    <th>Produit</th>
                    <th>PF QTY</th>
                    <th>Tester Qty</th>
                    <th>Set Qty</th>

                    <th>% Conformité</th>
                    <th style="display: none !important;">Total Qte</th>


                    <!--<th>Qty Fabrique</th>
                    <th> Statut OF </th> -->
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($fabOrders as $order)
                    <tr>
                        <td>{{ $order->OFID }}</td>
                        <td>{{ $order->client->name ?? 'N/A' }}</td>
                        <td>{{ $order->saleOrderId }}</td>
                        <td style="display:none">{{ $order->chaine->chef->name ?? 'Not Provided' }} }}</td>
                        <td>
                            <a href="#" class="product-link"
                               data-product-id="{{ $order->product->id }}"
                               data-product-name="{{ $order->product->product_name }}">
                                {{ $order->product->product_name ?? 'N/A' }}
                            </a>
                        </td>
                        <td>{{ $order->Pf_Qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Tester_qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Set_qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->totalQtyC ?? 0 }}</td>
                        <td style="display: none !important;"> class="total-qte">0</td>
                        <td>
                            <!-- Add Button with Icon -->
                            <button
                                class="btn btn-success btn-sm"
                                data-toggle="modal"
                                data-ref-id="{{ $order->product->ref_id ?? '' }}"
                                data-target="#editModal{{ $order->id }}"
                                title="Ajouter"
                            >
                                <i class="fas fa-plus"></i> <!-- Font Awesome "plus" icon -->
                            </button>

                            <!-- View Button with Icon -->
                            <button
                                class="btn btn-info btn-sm check-btn"
                                data-ofid="{{ $order->OFID }}"
                                title="Voir"
                            >
                                <i class="fas fa-eye"></i> <!-- Font Awesome "eye" icon -->
                            </button>

                            <!-- Delete Button with Icon (inside Form) 
                            <form
                                action="{{ route('quality.destroy', $order->id) }}"
                                method="POST"
                                style="display: inline;"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure?')"
                                    title="Supprimer"
                                >
                                    <i class="fas fa-trash-alt"></i> 
                                </button> -->
                            </form>
                        </td>
                    </tr>
                    <!-- Large Modal for Edit -->
                    <div class="modal fade" id="editModal{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content modal-content-demo">
                                <div class="modal-header">
                                    <h6 class="modal-title">Déclaration défaut OF: {{ $order->OFID }}</h6>

                                    <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <!-- Form Starts Here -->
                                <form action="{{ route('conformity-details.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="OFID" value="{{ $order->OFID }}">
                                    <div class="modal-body">
                                        <!-- Anomalies Dropdown -->
                                        <div class="form-group">
                                            <label for="AnoID_{{ $loop->index }}">Catégorie défaut</label>
                                            <select name="AnoId" id="AnoID_{{ $loop->index }}" class="form-control anomaly-dropdown" data-index="{{ $loop->index }}" required>
                                                <option value="">Selectionner catégorie</option>
                                                @foreach ($anomalies as $anomaly)
                                                    <option value="{{ $anomaly->AnoID }}">{{ $anomaly->Libele }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Composant Dropdown -->
                                        <div class="form-group">
                                            <label >Composant</label>
                                            <select name="Component" id="composantDropdown" class="form-control">
                                                <option value="">Selectionner produit</option>
                                            </select>
                                        </div>
                                        <!-- Default Entries Dropdown -->
                                        <div class="form-group">Liste défaut </label>
                                            <select name="Default" id="default_entries_{{ $loop->index }}" class="form-control default-entry-dropdown" required>
                                                <option value="">Selectionner le défaut</option>
                                            </select>
                                        </div>
                                        <!-- Type de Produit -->
                                        <div class="form-group">
                                            <label>Type de Produit</label>
                                            <select name="type_product" id="type_product" class="form-control" required>
                                                <option value="">Select Product Type</option>
                                                <option value="PF">PF</option>
                                                <option value="Tester">Tester</option>
                                                <option value="Set">Set</option>
                                            </select>
                                        </div>
                                        <!-- Quantity NC -->
                                        <div class="form-group">
                                            <label>Qté NC</label>
                                            <input type="number" name="Qty_NC" class="form-control" required>
                                        </div>
                                        <div style="display:none" class="form-group">
                                            <label>Of ID</label>
                                            <input type="number" id="fk_OFID" name="fk_OFID" class="form-control" value="{{ $order->id }}">
                                        </div>
                                        <!-- Responsibility -->
                                        <div class="form-group">
                                            <label>Responsabilité</label>
                                            <select name="RespDefaut" id="RespDefaut" class="form-control" required>
                                                <option value="">Select Responsibility</option>
                                                <option value="Main d'oeuvre">Main d'oeuvre</option>
                                                <option value="Machine">Machine</option>
                                                <option value="Fournisseur">Fournisseur</option>
                                            </select>
                                        </div>

                                        <!-- Comment -->
                                        <div class="form-group">
                                            <label>Commentaire</label>
                                            <input type="text" name="Comment" class="form-control" >
                                        </div>
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary submit-and-clear">Sauvgarder</button>
                                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Fermer</button>
                                    </div>
                                </form>
                                <!-- Form Ends Here -->
                            </div>
                        </div>
                    </div>
                    <!--End Large Modal -->
                @endforeach
                </tbody>
            </table>
        @endif
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <!-- Modal for Displaying Records -->
    <div class="modal fade" id="checkModal" tabindex="-1" role="dialog" aria-labelledby="checkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Défauts qualité de l'OF : <a class=" text-white bg-primary"> &nbsp; {{ $order->OFID }} &nbsp;</a></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <table class="table table-bordered table-fixed">
                        <thead>
                        <tr>
                            <th>Composant</th>
                            <th>Defaut</th>
                            <th style="width:7%">Qté NC</th>
                            <th style="width:7%">Type</th>
                            <th  style="width:13%">Responsibilité</th>
                            <!--<th>Date  </th> -->
                            <th>Commentaire</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="modalTableBody">
                        <!-- Records will be populated here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
<!-- End Modal for Displaying Records -->
    <!-- Edit Modal for Updating Conformity Details -->
    <div class="modal fade" id="editConformityModal" tabindex="-1" role="dialog" aria-labelledby="editConformityLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editConformityForm" method="POST" action="{{ route('conformity-details.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le défaut qualité</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_conformity_id">

                        <!-- 🔸 Anomalies Dropdown -->
                        <div class="form-group">
                            <label for="edit_anomaly">Catégorie défaut</label>
                            <select name="AnoId" id="edit_anomaly" class="form-control" required>
                                <option value="">Sélectionner catégorie</option>
                                @foreach ($anomalies as $anomaly)
                                    <option value="{{ $anomaly->AnoID }}">{{ $anomaly->Libele }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 🔸 Composant Dropdown -->
                        <div class="form-group">
                            <label for="edit_component_dropdown">Composant</label>
                            <select name="Component" id="edit_component_dropdown" class="form-control" required>
                                <option value="">Sélectionner un composant</option>
                            </select>
                        </div>

                        <!-- 🔸 Default Entries Dropdown -->
                        <div class="form-group">
                            <label for="edit_default_entries">Liste défaut</label>
                            <select name="Default" id="edit_default_entries" class="form-control" required>
                                <option value="">Sélectionner le défaut</option>
                            </select>
                        </div>

                        <!-- 🔸 Type Produit -->
                        <div class="form-group">
                            <label>Type Produit</label>
                            <select name="type_product" id="edit_type" class="form-control" required>
                                <option value="">Select</option>
                                <option value="PF">PF</option>
                                <option value="SF">SF</option>
                                <option value="Tester">Tester</option>
                                <option value="Set">Set</option>
                            </select>
                        </div>

                        <!-- 🔸 Quantité NC -->
                        <div class="form-group">
                            <label>Qté NC</label>
                            <input type="number" name="Qty_NC" id="edit_qty" class="form-control" required>
                        </div>
                        <!-- 🔸 Responsibility -->
                        <div class="form-group">
                            <label>Responsabilité</label>
                            <select name="RespDefaut" id="edit_resp" class="form-control" required>
                                <option value="">Select</option>
                                <option value="Main d'oeuvre">Main d'oeuvre</option>
                                <option value="Machine">Machine</option>
                                <option value="Fournisseur">Fournisseur</option>
                            </select>
                        </div>

                        <!-- 🔸 Commentaire -->
                        <div class="form-group">
                            <label>Commentaire</label>
                            <input type="text" name="Comment" id="edit_comment" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <!-- ✅ Load jQuery First -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ Load Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Load DataTables -->
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"></script>

    <script>
        $(document).on('click', '.submit-and-clear', function () {
            let $modal = $(this).closest('.modal');
            let $form = $modal.find('form');
            let actionUrl = $form.attr('action');

            if (!actionUrl) {
                alert('❌ Erreur: URL du formulaire non définie.');
                return;
            }

            $.ajax({
                url: actionUrl,
                type: $form.attr('method') || 'POST',
                data: $form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert('✅ Sauvegarde réussie');
                    $form[0].reset(); // Clear all fields
                },
                error: function (xhr) {
                    console.error('❌ AJAX error:', xhr.responseText);
                    alert('Erreur lors de l\'enregistrement.');
                }
            });
        });


        $(document).ready(function () {
            // ✅ Initialize DataTable
            if (!$.fn.DataTable.isDataTable('#qualityTable')) {
                var table = $('#qualityTable').DataTable({
                    responsive: true,
                    pageLength: 100,
                    autoWidth: false,
                    paging: true,
                    ordering: true,
                    searching: true,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
                    },
                    initComplete: function () {
                    let api = this.api();
                    let column = api.column(1); // Column index for Client
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
            }
            $('#qualityTable tbody tr').each(function () {
                let pf = parseFloat($(this).find('td').eq(5).text()) || 0;
                let tester = parseFloat($(this).find('td').eq(6).text()) || 0;
                let set = parseFloat($(this).find('td').eq(7).text()) || 0;
                let nc = parseFloat($(this).find('td').eq(8).text()) || 0; // totalQtyC

                let total = pf + tester + set;
                let conformity = total > 0 ? (((total - nc) / total) * 100).toFixed(2) + '%' : 'N/A';

                $(this).find('.total-qte').text(total);
                $(this).find('td').eq(8).text(conformity); // overwrite % Conformité cell
            });

            // ✅ Filter by Chef de Chaine
            $('#userFilter').on('change', function () {
                let selectedChaineId = $(this).val();
                table.column(3) // Ensure this matches the hidden 'chef' column index
                    .search(selectedChaineId)
                    .draw();
            });

            // ✅ Handle Component Dropdown on Modal Show
            $(document).on('show.bs.modal', '[id^=editModal]', function (event) {
                let button = $(event.relatedTarget);
                let refId = button.data('ref-id');
                let modal = $(this);
                let dropdown = modal.find('select#composantDropdown');

                if (!refId) {
                    alert('❌ Error: ref_id is missing');
                    return;
                }

                dropdown.empty().append('<option value="">Chargement...</option>');

                $.ajax({
                    url: '{{ route('get.components') }}',
                    type: 'GET',
                    data: { ref_id: refId },
                    success: function (data) {
                        dropdown.empty().append('<option value="">Sélectionner un composant</option>');
                        if (Array.isArray(data) && data.length > 0) {
                            $.each(data, function (index, component) {
                                dropdown.append(`<option value="${component.id}">[${component.component_code}] ${component.component_name}</option>`);
                            });
                        } else {
                            dropdown.append('<option disabled>Aucun composant trouvé</option>');
                        }
                    },
                    error: function (xhr) {
                        console.error('❌ AJAX Error:', xhr);
                        dropdown.empty().append('<option disabled>Erreur de chargement</option>');
                    }
                });
            });

            // ✅ Handle Default Entries Based on Anomaly Selection
            $(document).on('change', '.anomaly-dropdown', function () {
                var index = $(this).data('index');
                var anomalieId = $(this).val();
                var defaultEntriesDropdown = $('#default_entries_' + index);

                defaultEntriesDropdown.empty().append('<option value="">Select Default Entry</option>');

                if (anomalieId) {
                    $.ajax({
                        url: '/default-entries/' + anomalieId,
                        type: 'GET',
                        success: function (data) {
                            if (data.length > 0) {
                                $.each(data, function (index, entry) {
                                    defaultEntriesDropdown.append('<option value="' + entry.id + '">' + entry.label + '</option>');
                                });
                            } else {
                                defaultEntriesDropdown.append('<option value="">No entries found</option>');
                            }
                        }
                    });
                }
            });

            // ✅ Handle "Voir" Button (check-btn) Click
            $(document).on('click', '.check-btn', function () {
                var ofid = $(this).data('ofid');
                $('#modalTableBody').empty();

                $('#checkModal .modal-title').html(`Défauts qualité de l'OF : <a class="text-white bg-primary"> &nbsp; ${ofid} &nbsp;</a>`);

                $.ajax({
                    url: '/fetch-conformity-details',
                    type: 'GET',
                    data: { ofid: ofid },
                    success: function (response) {
                                $('#modalTableBody').empty(); // Always clear before appending

                        if (response.length > 0) {
                            response.forEach(function (record) {
                                var row = `
                                <tr>
                                    <td data-fulltext="${record.Component}">${record.Component}</td>
                                    <td data-fulltext="${record.default_label || 'N/A'}">${record.default_label || 'N/A'}</td>
                                    <td data-fulltext="${record.Qty_NC}">${record.Qty_NC}</td>
                                    <td data-fulltext="${record.type_product}">${record.type_product}</td>
                                    <td data-fulltext="${record.RespDefaut}">${record.RespDefaut}</td>
                                    <td data-fulltext="${record.Comment}">${record.Comment}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-conformity-btn"
                                            data-id="${record.id}"
                                            data-component="${record.Component}"
                                            data-default="${record.default_label}"
                                            data-qty="${record.Qty_NC}"
                                            data-type="${record.type_product}"
                                            data-resp="${record.RespDefaut}"
                                            data-comment="${record.Comment}"
                                            data-ref-id="${record.ref_id}"
data-anomaly-id="${record.anomaly_id}"
    data-default-id="${record.default_id}">

                                            Edit
                                        </button>
<button class="btn btn-sm btn-danger delete-conformity-btn"
            data-id="${record.id}">
            Delete
        </button>
                                    </td>
                                </tr>`;
                                $('#modalTableBody').append(row);
                            });

                            // ✅ Enable full text overlay if needed
                            $('#modalTableBody td').on('click', function () {
                                var fullText = $(this).attr('data-fulltext');
                                $('#textOverlay').text(fullText).show();
                            });
                        } else {
                            $('#modalTableBody').html('<tr><td colspan="9" class="text-center">No records found.</td></tr>');
                        }

                        $('#checkModal').modal('show');
                    },
                    error: function (xhr) {
                        console.error('Error fetching records:', xhr.responseText);
                        $('#modalTableBody').html('<tr><td colspan="9" class="text-center text-danger">An error occurred while fetching records.</td></tr>');
                    }
                });
            });

        });
        // ✅ Handle Edit Button in CheckModal Rows
        $(document).on('click', '.edit-conformity-btn', function () {
            let id = $(this).data('id');
            let component = $(this).data('component');
            let defaultLabel = $(this).data('default');
            let qty = $(this).data('qty');
            let type = $(this).data('type');
            let resp = $(this).data('resp');
            let comment = $(this).data('comment');
            let refId = $(this).data('ref-id'); // ✅ New refId from data attribute
            let anomalyId = $(this).data('anomaly-id') || ''; // if available
            let defaultId = $(this).data('default-id') || ''; // if available

            // Fill basic fields
            $('#edit_conformity_id').val(id);
            $('#edit_qty').val(qty);
            $('#edit_type').val(type);
            $('#edit_resp').val(resp);
            $('#edit_comment').val(comment);

            // 🔄 Load Components Dropdown
            $('#edit_component_dropdown').empty().append(`<option value="">Chargement...</option>`);
            $.ajax({
                url: '{{ route("dropdown.components") }}',
                type: 'GET',
                data: { ref_id: refId },
                success: function(data) {
                    $('#edit_component_dropdown').empty().append('<option value="">Sélectionner un composant</option>');
                    $.each(data, function(i, item) {
                        let selected = item.component_name === component ? 'selected' : '';
                        $('#edit_component_dropdown').append(`<option value="${item.id}" ${selected}>[${item.component_code}] ${item.component_name}</option>`);
                    });
                }
            });

            // 🔄 Preselect Anomaly (if available)
            if (anomalyId !== '') {
                $('#edit_anomaly').val(anomalyId).trigger('change');

                // Then load default entries for this anomaly
                $('#edit_default_entries').empty().append('<option value="">Chargement...</option>');
                $.ajax({
                    url: '/dropdown/default-entries/' + anomalyId,
                    type: 'GET',
                    success: function(data) {
                        $('#edit_default_entries').empty().append('<option value="">Sélectionner le défaut</option>');
                        $.each(data, function(i, item) {
                            let selected = item.id == defaultId ? 'selected' : '';
                            $('#edit_default_entries').append(`<option value="${item.id}" ${selected}>${item.label}</option>`);
                        });
                    }
                });
            }

            $('#editConformityModal').modal('show');
        });

        $(document).on('click', '.delete-conformity-btn', function () {
            let conformityId = $(this).data('id');
            if (confirm("Are you sure you want to delete this record?")) {
                $.ajax({
                    url: '/conformity-details/' + conformityId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        alert('Deleted successfully');
                        $('#checkModal').modal('hide');
                        // Optionally reload the page or re-trigger .check-btn click to refresh table
                    },
                    error: function(xhr) {
                        alert('Error deleting record');
                        console.error(xhr.responseText);
                    }
                });
            }
        });

    </script>
@endsection



