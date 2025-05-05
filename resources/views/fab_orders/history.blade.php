@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/css/history.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Fabrication History</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h4 class="card-title">Fabrication History</h4>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap" id="historyTable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>OFID</th>
                                <th>Lot Jus</th>
                                <th>Valid Date</th>
                                <th>Effectif Reel</th>
                                <th>Date Fabrication</th>
                                <th>PF Qty</th>
                                <th>SF Qty</th>
                                <th>Set Qty</th>
                                <th>Tester Qty</th>
                                <th>Comment Chaine</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($fabrications as $fabrication)
                                <tr>
                                    <td>{{ $fabrication->id }}</td>
                                    <td>{{ $fabrication->OFID }}</td>
                                    <td>{{ $fabrication->Lot_Jus ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Valid_date ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->effectif_Reel ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->date_fabrication ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Pf_Qty ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Sf_Qty ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Set_qty ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Tester_qty ?? 'NAN' }}</td>
                                    <td>{{ $fabrication->Comment_chaine ?? 'NAN' }}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-fabrication"
                                                data-id="{{ $fabrication->id }}"
                                                data-ofid="{{ $fabrication->OFID }}"
                                                data-lotjus="{{ $fabrication->Lot_Jus }}"
                                                data-validdate="{{ $fabrication->Valid_date }}"
                                                data-effectif="{{ $fabrication->effectif_Reel }}"
                                                data-datefabrication="{{ $fabrication->date_fabrication }}"
                                                data-pfqty="{{ $fabrication->Pf_Qty }}"
                                                data-sfqty="{{ $fabrication->Sf_Qty }}"
                                                data-setqty="{{ $fabrication->Set_qty }}"
                                                data-testerqty="{{ $fabrication->Tester_qty }}"
                                                data-comment="{{ $fabrication->Comment_chaine }}"
                                                data-toggle="modal" data-target="#editFabricationModal">
                                            Edit
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

    <!-- Edit Fabrication Modal -->
    <div class="modal fade" id="editFabricationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="updateFabricationForm" method="POST" action="{{ route('fabrication.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Fabrication Record</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" id="editFabricationId" name="id">

                        <div class="form-group">
                            <label>OFID</label>
                            <input type="text" id="editOFID" name="OFID" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Lot Jus</label>
                            <input type="text" id="editLotJus" name="Lot_Jus" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Valid Date</label>
                            <input type="date" id="editValidDate" name="Valid_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Effectif Reel</label>
                            <input type="number" id="editEffectifReel" name="effectif_Reel" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Date Fabrication</label>
                            <input type="datetime-local" id="editDateFabrication" name="date_fabrication" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>PF Qty</label>
                            <input type="number" id="editPfQty" name="Pf_Qty" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>SF Qty</label>
                            <input type="number" id="editSfQty" name="Sf_Qty" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Set Qty</label>
                            <input type="number" id="editSetQty" name="Set_qty" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Tester Qty</label>
                            <input type="number" id="editTesterQty" name="Tester_qty" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Comment Chaine</label>
                            <textarea id="editComment" name="Comment_chaine" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
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
            $('#historyTable').DataTable();

            $('.edit-fabrication').on('click', function () {
                // Get data from the clicked button
                var fabricationId = $(this).data('id');
                var ofid = $(this).data('ofid') || 'NAN';
                var lotjus = $(this).data('lotjus') || 'NAN';
                var validdate = $(this).data('validdate') || 'NAN';
                var effectif = $(this).data('effectif') || 'NAN';
                var datefabrication = $(this).data('datefabrication') || 'NAN';
                var pfqty = $(this).data('pfqty') || '0';
                var sfqty = $(this).data('sfqty') || '0';
                var setqty = $(this).data('setqty') || '0';
                var testerqty = $(this).data('testerqty') || '0';
                var comment = $(this).data('comment') || 'NAN';

                // Pre-fill the form inputs with retrieved data
                $('#editFabricationId').val(fabricationId);
                $('#editOFID').val(ofid);
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
