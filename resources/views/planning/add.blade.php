@extends('layouts.master')
@section('css')
@endsection
@section('page-header')
	<!-- breadcrumb -->
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">Pages</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ Empty</span>
			</div>
		</div>
		<div class="d-flex my-xl-auto right-content">
			<div class="pr-1 mb-3 mb-xl-0">
				<button type="button" class="btn btn-info btn-icon ml-2"><i class="mdi mdi-filter-variant"></i></button>
			</div>
			<div class="pr-1 mb-3 mb-xl-0">
				<button type="button" class="btn btn-danger btn-icon ml-2"><i class="mdi mdi-star"></i></button>
			</div>
			<div class="pr-1 mb-3 mb-xl-0">
				<button type="button" class="btn btn-warning  btn-icon ml-2"><i class="mdi mdi-refresh"></i></button>
			</div>
			<div class="mb-3 mb-xl-0">
				<div class="btn-group dropdown">
					<button type="button" class="btn btn-primary">14 Aug 2019</button>
					<button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" id="dropdownMenuDate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<span class="sr-only">Toggle Dropdown</span>
					</button>
					<div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownMenuDate" data-x-placement="bottom-end">
						<a class="dropdown-item" href="#">2015</a>
						<a class="dropdown-item" href="#">2016</a>
						<a class="dropdown-item" href="#">2017</a>
						<a class="dropdown-item" href="#">2018</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- breadcrumb -->
@endsection
@section('content')
	<!-- row -->
	<div class="row">
		<div class="col-md-8 offset-md-2">
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ajouter un planning
				</div>
				<div class="card-body">
					<form action="{{ route('planning.store') }}" method="POST">
						@csrf

						<!-- N_commande -->
						<div class="form-group">
							<label for="N_commande">N° Commande</label>
							<input type="text" name="N_commande" class="form-control" required>
						</div>

						<!-- Client_id (dropdown) -->
						<div class="form-group">
							<label for="Client_id">Client</label>
							<select name="Client_id" class="form-control" required>
								<option value="">-- Sélectionner un client --</option>
								@foreach($clients as $client)
									<option value="{{ $client->id }}">{{ $client->name }}</option>
								@endforeach
							</select>
						</div>

						<!-- date_Planif -->
						<div class="form-group">
							<label for="date_Planif">Date Planifiée</label>
							<input type="date" name="date_Planif" class="form-control">
						</div>

						<!-- date_debut -->
						<div class="form-group">
							<label for="date_debut">Date Début</label>
							<input type="date" name="date_debut" class="form-control">
						</div>

						<!-- date_fin -->
						<div class="form-group">
							<label for="date_fin">Date Fin</label>
							<input type="date" name="date_fin" class="form-control">
						</div>

						<!-- Instruction (textarea) -->
						<div class="form-group">
							<label for="Instruction">Instruction</label>
							<textarea name="Instruction" class="form-control" rows="4"></textarea>
						</div>

						<!-- Submit Button -->
						<button type="submit" class="btn btn-primary">Enregistrer</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- row closed -->
	</div>
	<!-- Container closed -->
	</div>
	<!-- main-content closed -->
@endsection
@section('js')
@endsection