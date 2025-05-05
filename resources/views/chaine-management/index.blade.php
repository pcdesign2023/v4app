@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">List des chaines de production</span>
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
                        <a class="btn ripple btn-primary" data-target="#modaldemo1" data-toggle="modal" href="">Ajouter une chaine</a>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap">
                            <thead>
                            <thead>
                            <tr>
                                <th>Num Chaine</th>
                                <th>Chef de Chaine</th>
                                <th>Agent Qualité</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($chaines as $chaine)
                                <tr>
                                    <td>{{ $chaine->Num_chaine }}</td>
                                    <td>{{ $chaine->chefDeChaine ? $chaine->chefDeChaine->name : 'N/A' }}</td>
                                    <td>{{ $chaine->responsableQLTY ? $chaine->responsableQLTY->name : 'N/A' }}</td>
                                    <td>
                                        <!-- Update Button -->
                                        <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#updateChaineModal{{ $chaine->id }}">Modifier</button>

                                        <!-- Delete Button -->
                                        <form action="{{ route('chaine.destroy', $chaine->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this chaine?')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Update Modal -->
                                <div class="modal fade" id="updateChaineModal{{ $chaine->id }}" tabindex="-1" role="dialog" aria-labelledby="updateChaineModalLabel{{ $chaine->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateChaineModalLabel{{ $chaine->id }}">Update Chaine</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('chaine.update', $chaine->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="Num_chaine">Num Chaine</label>
                                                        <input type="number" id="Num_chaine" name="Num_chaine" class="form-control" value="{{ $chaine->Num_chaine }}" required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="responsable_QLTY_id">Agent Qualité</label>
                                                        <select id="responsable_QLTY_id" name="responsable_QLTY_id" class="form-control">
                                                            <option value="">None</option>
                                                            @foreach ($responsablesQLTY as $user)
                                                                <option value="{{ $user->id }}" {{ $chaine->responsable_QLTY_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="chef_de_chaine_id">Chef de Chaine</label>
                                                        <select id="chef_de_chaine_id" name="chef_de_chaine_id" class="form-control">
                                                            <option value="">None</option>
                                                            @foreach ($chefsDeChaine as $user)
                                                                <option value="{{ $user->id }}" {{ $chaine->chef_de_chaine_id == $user->id ? 'selected' : '' }}>
                                                                    {{ $user->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>



                                                    <button type="submit" class="btn btn-primary">Update Chaine</button>
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


    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
    <!-- Basic modal -->
    <div class="modal" id="modaldemo1">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">Add New chaine</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-content">

                    <div class="modal-body">
                        <form action="{{ route('chaine.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="Num_chaine">Num Chaine</label>
                                <input type="number" id="Num_chaine" name="Num_chaine" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="responsable_QLTY_id">Agent Qualité</label>
                                <select id="responsable_QLTY_id" name="responsable_QLTY_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach ($responsablesQLTY as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="chef_de_chaine_id">Chef de Chaine</label>
                                <select id="chef_de_chaine_id" name="chef_de_chaine_id" class="form-control">
                                    <option value="">None</option>
                                    @foreach ($chefsDeChaine as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" style="display:none ">
                                <label for="nbr_operateur">Nbr Operateur</label>
                                <input type="number" Value="20"id="nbr_operateur" name="nbr_operateur" class="form-control" >
                            </div>

                            <button type="submit" class="btn btn-primary">Create Chaine</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Basic modal -->


@endsection
@section('js')
@endsection
