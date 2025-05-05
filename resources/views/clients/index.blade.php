@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"></h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"></span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <!--div-->
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
                        <a class="btn ripple btn-primary" data-target="#addClientModal" data-toggle="modal" href="">Ajouter un client  </a>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap">
                            <thead>
                            <tr>
                                <th>Client</th>
                                <th>Instruction</th>
                                <th>Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client->name }}</td>
                                    <td>{{ $client->instruction }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button data-target="#editModal{{ $client->id }}" data-toggle="modal" class="btn btn-success ">
                                            Modifier</i>
                                        </button>
                                        <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger " onclick="return confirm('Delete this client?')">Supprimer</button>
                                        </form>
                                        </a>

                                    </td>
                                </tr>

                                <!-- Edit Modal for Each Client -->
                                <div class="modal fade" id="editModal{{ $client->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $client->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <!-- Update Form -->
                                                <form action="{{ route('clients.update', $client) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="form-group">
                                                        <label for="name{{ $client->id }}">Raison social</label>
                                                        <input type="text" id="name{{ $client->id }}" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="instruction{{ $client->id }}">Instruction</label>
                                                        <textarea id="instruction{{ $client->id }}" name="instruction" class="form-control" required>{{ old('instruction', $client->instruction) }}</textarea>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">Modifier Client</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach

                            </tbody>
                        </table>
                    </div><!-- bd -->
                </div><!-- bd -->
            </div><!-- bd -->
        </div>
        <!--/div-->
        <div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addClientModalLabel">Ajouter Client</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('clients.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Raison social</label>
                                <input type="text" id="name" name="name" class="form-control"  required>
                            </div>
                            <div class="form-group">
                                <label for="instruction">Instruction</label>
                                <input type="text" id="instruction" name="instruction" class="form-control"  required>
                            </div>
                            <button type="submit" class="btn btn-primary">Ajouter Client</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->






@endsection
@section('js')
@endsection
