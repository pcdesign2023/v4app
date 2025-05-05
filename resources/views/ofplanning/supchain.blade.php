@extends('layouts.master')

@section('css')
	<link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
	<link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
	<link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endsection

@section('page-header')
	<div class="breadcrumb-header justify-content-between">
		<div class="my-auto">
			<div class="d-flex">
				<h4 class="content-title mb-0 my-auto">ORDRES DE FABRICATION</h4>
			</div>
		</div>
	</div>
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
					<div class="d-flex justify-content-end mb-3">

					</div>


					<div class="table-responsive">
						<table class="table text-md-nowrap" id="fabOrdersTable">
							<thead>
							<tr>
								<th>Client</th>
								<th>Commande</th>
								<th>OFID</th>
								<th>Produit</th>
								<th>Date Planifiée</th>
								<th>Quantité Planifiée</th>
								<th>Quantité Réel</th>
								<th>Priorité</th>
								<th>Statut</th>
								<th>Instruction</th>
								<th>Action</th>
							</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="ofModal" tabindex="-1" role="dialog" aria-labelledby="ofModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<form id="ofForm" action="{{ route('ofplanning.store') }}" method="POST" class="modal-content needs-validation" novalidate>
				<input type="hidden" name="id" id="of_id">
				<input type="hidden" name="prod_des" id="prod_des">

				@csrf

				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title" id="ofModalLabel">Ajouter une commande</h5>
					<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
						<span>&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<div id="formResponse" class="alert d-none mt-2"></div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Référence OF</label>
							<input type="text" name="OFID" class="form-control" required>
						</div>

						<div class="form-group col-md-6">
							<label>Client</label>
							<select name="client" class="form-control" required>
								@foreach($clients as $client)
									<option value="{{ $client->name }}">{{ $client->name }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Produit</label>
							<select name="prod_ref" class="form-control" required onchange="updateProductDesc(this)">
								@foreach($products->unique('ref_id') as $product)
									<option value="{{ $product->ref_id }}" data-search="{{ $product->ref_id }}"
											data-descomp="{{ $product->product_name }}">
										{{ $product->product_name }}
									</option>
								@endforeach
							</select>
						</div>

						<div class="form-group col-md-6">
							<label>Commande</label>
							<select name="commande" class="form-control" required>
								@foreach($commandes as $cmd)
									<option value="{{ $cmd }}">{{ $cmd }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Qty Planifié</label>
							<input type="number" name="qte_plan" class="form-control" required min="1">
						</div>

						<div class="form-group col-md-6">
							<label>Qty Réel</label>
							<input type="number" name="qte_reel" class="form-control" disabled min="0">
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Statut</label>
							<select name="statut" class="form-control" required>
								<option value="Planifié">Planifié</option>
								<option value="En cours">En cours</option>
								<option value="Réalisé">Réalisé</option>
								<option value="Traité">Traité</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>Date Planifiée</label>
							<input type="date" name="date_planifie" class="form-control" required>
						</div>
					</div>


					<div class="form-group">
						<label>Instruction</label>
						<textarea name="instruction" class="form-control" rows="2" maxlength="500"></textarea>
					</div>

					<div class="form-group">
						<label>Commentaire</label>
						<textarea name="comment" class="form-control" rows="3" maxlength="800"></textarea>
					</div>
				</div>

				<div class="modal-footer bg-light">
					<button type="submit" id="modalSubmitBtn" class="btn btn-success">
						💾 Enregistrer
					</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
				</div>
			</form>
		</div>
	</div>

@endsection

@section('js')
	<script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
	<script>

		$(document).ready(function () {
			// Init DataTable
			$('#fabOrdersTable').DataTable({
				destroy: true,
				processing: true,
				serverSide: false,
				ajax: "{{ route('ofplanning.supchain') }}",
				pageLength: 100,
				columns: [
					{ data: 'client' },
					{ data: 'commande' },
					{ data: 'OFID' },
					{ data: 'prod_des' },
					{ data: 'date_planifie' },
					{ data: 'qte_plan' },
					{ data: 'qte_reel' },
					{ data: 'priority' },
					{ data: 'statut' },
					{ data: 'instruction' },
					{
						data: null,
						orderable: false,
						searchable: false,
						render: function (data, type, row) {
							return `
				<button class="btn btn-sm btn-primary edit-mp-btn" data-id="${row.id}">
					<i class="mdi mdi-pencil"></i>
				</button>
			`;
						}
					}
				],
				language: {
					url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"
				}
			});

			// Ensure prod_des is initialized on page load
			const initialSelect = document.querySelector('[name="prod_ref"]');
			if (initialSelect) {
				updateProductDesc(initialSelect);
			}

			// Handle AJAX form submission
			$('#ofForm').on('submit', function (e) {
				e.preventDefault();
				const form = $(this);
				const url = form.attr('action');
				const formData = form.serialize();

				$('#formResponse').removeClass('alert-success alert-danger').addClass('d-none').text('');

				$.ajax({
					type: "POST",
					url: url,
					data: formData,
					success: function (response) {
						$('#formResponse')
								.removeClass('d-none')
								.addClass('alert alert-success')
								.text('Commande ajoutée avec succès.');

						if ($.fn.DataTable.isDataTable('#fabOrdersTable')) {
							$('#fabOrdersTable').DataTable().ajax.reload();
						}

						setTimeout(function () {
							$('#ofModal').modal('hide');
							form[0].reset();
							updateProductDesc(document.querySelector('[name="prod_ref"]'));
						}, 1000);
					},
					error: function (xhr) {
						let message = "Erreur lors de l'ajout.";
						if (xhr.responseJSON && xhr.responseJSON.message) {
							message = xhr.responseJSON.message;
						}
						$('#formResponse')
								.removeClass('d-none')
								.addClass('alert alert-danger')
								.text(message);
					}
				});
			});
		});

		function matchCustom(term, text, option) {
			const searchText = $(option).data('search') || '';
			return searchText.toLowerCase().indexOf(term.toLowerCase()) > -1;
		}

		$('#ofModal').on('shown.bs.modal', function () {
			$('[name="prod_ref"]').select2({
				placeholder: "Sélectionner un produit",
				width: '100%',
				allowClear: true,
				dropdownParent: $('#ofModal'),
				matcher: function(params, data) {
					if ($.trim(params.term) === '') {
						return data;
					}
					if (typeof data.element === 'undefined') {
						return null;
					}
					const $element = $(data.element);
					const searchText = $element.data('search') || '';
					if (searchText.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
						return data;
					}
					return null;
				}
			});

			$('[name="commande"]').select2({
				placeholder: "Sélectionner une commande",
				width: '100%',
				allowClear: true,
				dropdownParent: $('#ofModal')
			});
		});
		$('#ofForm')
				.find('input:not([name="_token"]), select, textarea')
				.prop('disabled', true);
		function updateProductDesc(select) {
			const selectedOption = select.options[select.selectedIndex];
			const descomp = selectedOption.getAttribute('data-descomp');
			document.getElementById('prod_des').value = descomp;
		}
		$(document).on('click', '.delete-btn', function () {
			const id = $(this).data('id');

			if (confirm('Voulez-vous vraiment supprimer cette commande ?')) {
				$.ajax({
					url: `/ofplanning/${id}`,
					type: 'DELETE',
					data: {
						_token: '{{ csrf_token() }}'
					},
					success: function (response) {
						alert(response.message);
						$('#fabOrdersTable').DataTable().ajax.reload();
					},
					error: function () {
						alert('Erreur lors de la suppression.');
					}
				});
			}
		});
		$(document).on('click', '.edit-btn', function () {
			const id = $(this).data('id');

			$.ajax({
				url: `/ofplanning/${id}`,
				type: 'GET',
				success: function (data) {
					// Fill form fields
					$('#of_id').val(data.id);
					$('[name="OFID"]').val(data.OFID);
					$('[name="prod_ref"]').val(data.prod_ref);
					$('#prod_des').val(data.prod_des);
					$('[name="client"]').val(data.client);
					$('[name="commande"]').val(data.commande);
					$('[name="qte_plan"]').val(data.qte_plan);
					$('[name="qte_reel"]').val(data.qte_reel);
					$('[name="statut"]').val(data.statut);
					$('[name="date_planifie"]').val(data.date_planifie);
					$('[name="instruction"]').val(data.instruction);
					$('[name="comment"]').val(data.comment);
					$('#ofModalLabel').text('Modifier la commande');
					$('#modalSubmitBtn').html('✏️ Mettre à jour');
					$('#ofModal').modal('show');
				},
				error: function () {
					alert('Erreur lors du chargement des données.');
				}
			});
		});

		$('.btn-primary[data-target="#ofModal"]').on('click', function () {
			$('#ofForm')[0].reset();
			$('#of_id').val('');
			$('#formResponse').addClass('d-none').text('');

			$('#ofModalLabel').text('Ajouter une commande');
			$('#modalSubmitBtn').html('💾 Enregistrer');

			updateProductDesc(document.querySelector('[name="prod_ref"]'));
		});
		$(document).on('click', '.edit-mp-btn', function () {
			const id = $(this).data('id');

			$.ajax({
				url: `/ofplanning/${id}`,
				type: 'GET',
				success: function (data) {
					// Fill form fields
					$('#of_id').val(data.id);
					$('[name="OFID"]').val(data.OFID);
					$('[name="prod_ref"]').val(data.prod_ref).trigger('change');
					$('#prod_des').val(data.prod_des);
					$('[name="client"]').val(data.client);
					$('[name="commande"]').val(data.commande).trigger('change');
					$('[name="qte_plan"]').val(data.qte_plan);
					$('[name="qte_reel"]').val(data.qte_reel);
					$('[name="statut"]').val(data.statut);
					$('[name="date_planifie"]').val(data.date_planifie);
					$('[name="instruction"]').val(data.instruction);
					$('[name="comment"]').val(data.comment);

					// Disable all fields
					//$('#ofForm input, #ofForm select, #ofForm textarea').prop('disabled', true);
					$('#ofForm')
							.find('input:not([name="_token"]), select, textarea')
							.prop('readonly', true)
							.prop('disabled', false);
					// Enable only statut and qte_reel
					$('[name="statut"], [name="qte_reel"]').prop('readonly', false);

					// Update modal header and button
					$('#ofModalLabel').text('Mettre à jour le statut / quantité');
					$('#modalSubmitBtn').html('✔️ Enregistrer');

					// Show modal
					$('#ofModal').modal('show');
				},
				error: function () {
					alert('Erreur lors du chargement des données.');
				}
			});
		});

	</script>

@endsection
