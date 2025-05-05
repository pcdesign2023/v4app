@extends('layouts.master')

@section('content')
    <div class="container mt-5">
        <h2>Quality Tickets (Completed Orders)XX</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($fabOrders->isEmpty())
            <p>No completed orders found.</p>
        @else
            <table class="table table-striped">
                <tr>
                    <th>OF ID</th>
                    <th>Product</th>
                    <th>PF QTY</th>
                    <th>SF Qty</th>
                    <th>Tester Qty</th>
                    <th>Set Qty</th>
                    <th>Start Prod</th>
                    <th>End Prod</th>
                    <th>Qty Fabrique</th>
                    <th>Statut OF</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($fabOrders as $order)
                    <tr>
                        <td>{{ $order->OFID }}</td>
                        <td>
                            <a href="#" class="product-link"
                               data-product-id="{{ $order->product->id }}"
                               data-product-name="{{ $order->product->product_name }}">
                                {{ $order->product->product_name ?? 'N/A' }}
                            </a>
                        </td>
                        <td>{{ $order->Pf_Qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Sf_Qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Tester_qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Set_qty ?? 'Not Provided' }}</td>
                        <td>{{ $order->Start_Prod ? \Carbon\Carbon::parse($order->Start_Prod)->format('d/m/Y') : 'Not Provided' }}</td>
                        <td>{{ $order->End_Prod ? \Carbon\Carbon::parse($order->End_Prod)->format('d/m/Y') : 'Not Provided' }}</td>
                        <td>{{ $order->Qty_fabrique ?? 0 }}</td>
                        <td>
                                            <span class="badge badge-{{ $order->Statut_of == 'Completed' ? 'success' : 'warning' }}">
                                                {{ $order->Statut_of ?? 'Not Provided' }}
                                            </span>
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModal{{ $order->id }}">Edit</button>

                            <!-- Check Button -->
                            <button class="btn btn-info btn-sm check-btn" data-quality-id="{{ $order->id }}">Check</button>

                            <!-- Delete Button -->
                            <form action="{{ route('quality.destroy', $order->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>

                    @include('partials.edit_modal', ['order' => $order]) <!-- Include Edit Modal -->
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @include('partials.check_modal') <!-- Include Check Modal -->
@endsection
