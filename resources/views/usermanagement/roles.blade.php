@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">Gestion des Roles</h4>
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
                                    <a class="btn ripple btn-primary" data-target="#modaldemo1" data-toggle="modal" href="">Créer un role</a>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table table-striped mg-b-0 text-md-nowrap">
										<thead>
											<tr>
												<th>ID</th>
												<th>libellé</th>
                                                <th>action</th>

                                            </tr>
										</thead>
										<tbody>
                                        @foreach ($roles as $role)
                                            <tr>
                                                <th scope="row">{{ $role->id }}</th>
                                                <td>{{ $role->label }}</td>
                                                <td>
                                                    <!-- Edit Button -->
                                                    <button data-target="#editModal{{ $role->id }}" data-toggle="modal" class="btn btn-success btn-icon">
                                                        <i class="typcn typcn-edit"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Edit Modal for Each Role -->
                                            <div class="modal fade" id="editModal{{ $role->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $role->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editModalLabel{{ $role->id }}">Modifier Role</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Update Form -->
                                                            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="form-group">
                                                                    <label for="label{{ $role->id }}">Role Libele</label>
                                                                    <input type="text" class="form-control" id="label{{ $role->id }}" name="label" value="{{ old('label', $role->label) }}" required>
                                                                </div>

                                                                <button type="submit" class="btn btn-primary">Modifier Role</button>
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
                                <h6 class="modal-title">Création d'un nouveau role</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('roles.store') }}" method="POST">
                                    @csrf
                                    <div class="row row-xs">

                                        <div class="col-md-5 mg-t-10 mg-md-t-0">
                                            <input placeholder="Role " class="form-control" type="text" id="label" name="label" value="{{ old('label') }}" required>
                                        </div>
                                        <div class="col-md mt-4 mt-xl-0">
                                            <button class="mt-1  btn ripple btn-primary" type="submit">Créer role </button>
                                        </div>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- End Basic modal -->


@endsection
@section('js')
@endsection
