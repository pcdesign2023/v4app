@extends('layouts.master')

@section('css')
    <!-- Bootstrap & DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">List des Défauts</h4>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    @if (session('success'))
                        <p style="color: green;">{{ session('success') }}</p>
                    @endif
                    @if ($errors->any())
                        <div style="color: red;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addAnomalyModal">
                            Ajouter une Catégorie
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap" id="anomaliesTable">
                            <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($anomalies as $anomaly)
                                <tr>
                                    <td>{{ $anomaly->Libele }}</td>
                                    <td>
                                        <!-- Update Button -->
                                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateAnomalyModal{{ $anomaly->AnoID }}">
                                            Modifier catégorie
                                        </button>
                                        <!-- Delete Button with Confirmation -->
                                        <form action="{{ route('anomalies.destroy', $anomaly->AnoID) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" type="submit" onclick="return confirm('Vous êtes sur de vouloir supprimer la catégorie ?')">
                                                Supprimer catégorie
                                            </button>
                                        </form>
                                        <!-- Add Default Button -->
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addDefaultModal{{ $anomaly->AnoID }}">
                                            Ajouter défaut
                                        </button>
                                        <!-- View Default Entries Button -->
                                        <button class="btn btn-info btn-sm view-default-entries" data-toggle="modal"
                                                data-target="#defaultEntriesModal" data-anoid="{{ $anomaly->AnoID }}" data-anomaly-name="{{ $anomaly->Libele }}">
                                            Afficher les défauts
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal for Adding Default Entry -->
                                <div class="modal fade" id="addDefaultModal{{ $anomaly->AnoID }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('default_entries.store', ['Anoid' => $anomaly->AnoID]) }}" method="POST">
                                                @csrf

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ajouter défaut catégorie : {{ $anomaly->Libele }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <input type="hidden" name="AnoID" value="{{ $anomaly->AnoID }}">
                                                    <div class="form-group">
                                                        <label for="id">Code</label>
                                                        <input type="number" name="id" class="form-control" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="label">Description</label>
                                                        <input type="text" name="label" class="form-control" required>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Ajouter</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Update Anomaly Modal -->
                                <div class="modal fade" id="updateAnomalyModal{{ $anomaly->AnoID }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('anomalies.update', $anomaly->AnoID) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Modifier catégorie</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Libele</label>
                                                        <input type="text" name="Libele" class="form-control" value="{{ $anomaly->Libele }}" required>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Modifier</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Anomaly Modal -->
    <div class="modal fade" id="addAnomalyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('anomalies.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Add Anomaly</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Libele</label>
                            <input type="text" name="Libele" class="form-control" required>
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
    <!-- Modal to Display Default Entries -->
    <div class="modal fade" id="defaultEntriesModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Liste des défauts catégorie :<span id="modalAnomalyLabel"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table" id="defaultEntriesTable">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3">Select an anomaly to view its default entries.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Default Entry Modal -->
    <div class="modal fade" id="updateDefaultEntryModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="updateModalLabel">
                        <i class="fas fa-edit mr-2"></i>Modifier le défaut
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="updateDefaultEntryForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <!-- Hidden input to store old ID -->
                        <input type="hidden" id="updateOldEntryId" name="old_id">

                        <div class="form-group">
                            <label for="updateEntryId" class="font-weight-bold">
                                <i class="fas fa-hashtag mr-1"></i>Code
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="number"
                                       id="updateEntryId"
                                       name="id"
                                       class="form-control"
                                       required
                                       min="0"
                                       placeholder="Entrez le code">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="updateLabel" class="font-weight-bold">
                                <i class="fas fa-tag mr-1"></i>Description
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-font"></i></span>
                                </div>
                                <input type="text"
                                       id="updateLabel"
                                       name="label"
                                       class="form-control"
                                       required
                                       placeholder="Entrez la description">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



@endsection

@section('js')
    <script>

        // Open Update Modal & Load Selected Entry Data
        $(document).on('click', '.edit-default-entry', function () {
            var entryId = $(this).data('id');
            var entryLabel = $(this).data('label');

            // Set both new and old IDs
            $('#updateOldEntryId').val(entryId);
            $('#updateEntryId').val(entryId);
            $('#updateLabel').val(entryLabel);

            $('#updateDefaultEntryModal').modal('show');
        });

    </script>
    <script>
        $(document).ready(function () {
            // Load Default Entries for Selected Anomaly
            $('.view-default-entries').click(function () {
                var anoid = $(this).data('anoid');
                var anomalyName = $(this).data('anomaly-name');

                $('#modalAnomalyLabel').text(anomalyName);
                $('#defaultEntriesTable tbody').html('<tr><td colspan="3">Loading...</td></tr>');

                $.ajax({
                    url: '/default-entries/get/' + anoid,
                    type: 'GET',
                    success: function (response) {
                        var html = response.length
                            ? response.map(entry => `
                        <tr>
                            <td>${entry.id}</td>
                            <td>${entry.label}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-default-entry"
                                    data-id="${entry.id}" data-label="${entry.label}">
                                    Editer
                                </button>
                            </td>
                        </tr>`).join('')
                            : '<tr><td colspan="3">No default entries found.</td></tr>';

                        $('#defaultEntriesTable tbody').html(html);
                    },
                    error: function () {
                        $('#defaultEntriesTable tbody').html('<tr><td colspan="3">Error loading data.</td></tr>');
                    }
                });
            });

            // Open Update Modal & Load Selected Entry Data
            $(document).on('click', '.edit-default-entry', function () {
                var entryId = $(this).data('id');
                var entryLabel = $(this).data('label');

                $('#updateEntryId').val(entryId);
                $('#updateLabel').val(entryLabel);

                $('#updateDefaultEntryModal').modal('show');
            });

            // Submit Update Form via AJAX
            // Submit Update Form via AJAX
            $('#updateDefaultEntryForm').submit(function (e) {
                e.preventDefault();

                var oldId = $('#updateOldEntryId').val(); // Use hidden input for old ID
                var formData = $(this).serialize();

                $.ajax({
                    url: '/default-entries/update/' + oldId, // Pass old ID in URL
                    type: 'PUT',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#updateDefaultEntryModal').modal('hide');
                        $.notify({ message: response.message }, { type: "success", delay: 2000 });
                        $('.view-default-entries').trigger('click'); // Refresh the table
                    },
                    error: function (xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors && errors.id) {
                            alert(errors.id[0]); // Show the validation error
                        } else {
                            alert("An error occurred. Please try again.");
                        }
                    }
                });
            });



        });


    </script>
    <script>
        function showNotification(message, type) {
            toastr[type](message);
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>

@endsection
