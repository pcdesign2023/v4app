<div>
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">BOM Materials for {{ $productName }}</h5>
                        <button type="button" class="close" wire:click="$set('showModal', false)">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Component Name</th>
                                <th>Component Code</th>
                                <th>Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($bomMaterials as $material)
                                <tr>
                                    <td>{{ $material->component_name ?? 'N/A' }}</td>
                                    <td>{{ $material->component_code ?? 'N/A' }}</td>
                                    <td>{{ $material->quantity ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No materials found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
