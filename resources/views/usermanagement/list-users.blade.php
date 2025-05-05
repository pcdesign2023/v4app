@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Liste des utilisateurs</h4>
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
                        <a class="btn ripple btn-primary" data-target="#addUserModal" data-toggle="modal" href="">Créer un utilisateur</a>
                        <i class="mdi mdi-dots-horizontal text-gray"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mg-b-0 text-md-nowrap">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>email</th>
                                <th>Position</th>
                                <th>Action</th>


                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $user->id }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role ? $user->role->label : 'No Role Assigned' }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button data-target="#editModal{{ $user->id }}" data-toggle="modal" class="btn btn-success btn-icon">
                                            <i class="typcn typcn-edit"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal for Each User -->
                                <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <!-- Update Form -->
                                                <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group">
                                                        <label for="name{{ $user->id }}">Nom & Prénom</label>
                                                        <input type="text" id="name{{ $user->id }}" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email{{ $user->id }}">Email</label>
                                                        <input type="email" id="email{{ $user->id }}" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password{{ $user->id }}">Mot de passe</label>
                                                        <input type="password" id="password{{ $user->id }}" name="password" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="password_confirmation{{ $user->id }}">Confirmer Mot de passe</label>
                                                        <input type="password" id="password_confirmation{{ $user->id }}" name="password_confirmation" class="form-control">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="role{{ $user->id }}">Role</label>
                                                        <select id="role{{ $user->id }}" name="role_id" class="form-control" required>
                                                            <option value="">Select a Role</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->id }}" {{ $role->id == $user->role_id ? 'selected' : '' }}>
                                                                    {{ $role->label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Modifier</button>
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
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nom & Prénom</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmer Mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role_id" class="form-control" required>
                                <option value="">Selectionner le Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>






@endsection
@section('js')
@endsection
