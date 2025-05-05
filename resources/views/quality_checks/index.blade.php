@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Quality Checks</h2>
        <a href="{{ route('quality_checks.create') }}" class="btn btn-primary mb-3">New Quality Check</a>

        <table class="table">
            <thead>
            <tr>
                <th>Fabrication Order</th>
                <th>Conform</th>
                <th>Non-Conform</th>
                <th>Checked By</th>
                <th>Date</th>
            </tr>
            </thead>
            <tbody>
            @foreach($qualityChecks as $qc)
                <tr>
                    <td>{{ $qc->fabricationOrder->product->name ?? '-' }}</td>
                    <td>{{ $qc->quantity_conform }}</td>
                    <td>{{ $qc->quantity_nonconform }}</td>
                    <td>{{ $qc->user->name ?? '-' }}</td>
                    <td>{{ $qc->created_at->format('Y-m-d') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
