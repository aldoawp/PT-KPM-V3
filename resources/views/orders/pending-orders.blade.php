@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @if (session()->has('success'))
                    <div class="alert text-white bg-success" role="alert">
                        <div class="iq-alert-text">{{ session('success') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endif
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="mb-3">Daftar Pesanan Baru</h4>
                    </div>
                    <div>
                        <a href="{{ route('order.pendingOrders') }}" class="btn btn-danger add-list"><i
                                class="fa-solid fa-trash mr-3"></i>Hapus Pencarian</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('order.pendingOrders') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="form-group row">
                            <label for="row" class="col-sm-3 align-self-center">Row:</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="row">
                                    <option value="10" @if (request('row') == '10') selected="selected" @endif>10
                                    </option>
                                    <option value="25" @if (request('row') == '25') selected="selected" @endif>25
                                    </option>
                                    <option value="50" @if (request('row') == '50') selected="selected" @endif>50
                                    </option>
                                    <option value="100" @if (request('row') == '100') selected="selected" @endif>100
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label col-sm-3 align-self-center" for="search">Search:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="search" class="form-control" name="search"
                                        placeholder="Cari pesanan..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text bg-primary"><i
                                                class="fa-solid fa-magnifying-glass font-size-20"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <div class="table-responsive rounded mb-3">
                    <table class="table mb-0">
                        <thead class="bg-white text-uppercase">
                            <tr class="ligth ligth-data">
                                <th>No.</th>
                                <th>No Invoice</th>
                                <th>@sortablelink('customer.name', 'Nama')</th>
                                <th>@sortablelink('created_at', 'Tanggal Pesanan')</th>
                                <th>Pembayaran</th>
                                <th>@sortablelink('total', 'total')</th>
                                <th>Sales</th>
                                <th>Region</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $orders->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>{{ $order->invoice_no }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
                                    <td>{{ Str::title($order->payment_status) }}</td>
                                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ Str::title($order->customer->branch->region) }}</td>
                                    <td>
                                        <span class="badge badge-danger">{{ $order->order_status }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a href="{{ route('order.pending.deleteOrder', $order->id) }}"
                                                class="btn btn-danger border-none mr-2" data-toggle="tooltip"
                                                data-placement="top" title="" data-original-title="Delete"><i
                                                    class="fa-solid fa-trash mr-0"></i></a>
                                            <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Details"
                                                href="{{ route('order.orderDetails', $order->id) }}">Details
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $orders->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
