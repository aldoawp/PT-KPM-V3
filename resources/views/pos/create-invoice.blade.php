@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-header d-flex justify-content-between bg-primary">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">Invoice</h4>
                        </div>

                        <div class="invoice-btn d-flex">
                            <button type="button" class="btn btn-primary-dark mr-2" data-toggle="modal"
                                data-target=".bd-example-modal-lg">Konfirmasi</button>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">Invoice dari
                                                {{ $customer->name }}<br />Total Tagihan: Rp
                                                {{ number_format($productItem->total(), 0, ',', '.') }}</h3>
                                        </div>
                                        <form action="{{ route('pos.sales.storeOrder') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="payment_status">Jenis Pembayaran</label>
                                                        <select
                                                            class="form-control @error('payment_status') is-invalid @enderror"
                                                            name="payment_status">
                                                            <option selected="" disabled="">-- Pilih Pembayaran --
                                                            </option>
                                                            <option value="tunai">Tunai</option>
                                                            <option value="cek">Cek</option>
                                                            <option value="bon">Bon</option>
                                                        </select>
                                                        @error('payment_status')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pay">Dibayarkan Sekarang</label>
                                                        <input type="number"
                                                            class="form-control @error('pay') is-invalid @enderror"
                                                            id="pay" name="pay" value="{{ old('pay') }}"
                                                            min="0" max="{{ $productItem->total() }}" placeholder="0" required>
                                                        @error('pay')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <img src="{{ asset('assets/images/logo-kpm.png') }}" class="logo-invoice img-fluid mb-3">
                                <h5 class="mb-3">Hello, {{ $customer->name }}</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive-sm">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Tanggal</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Alamat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ Carbon\Carbon::now()->format('M d, Y') }}</td>
                                                <td><span class="badge badge-danger">Belum Dibayar</span></td>
                                                <td>
                                                    <p class="mb-0">{{ $customer->address }}<br>
                                                        Shop Name:
                                                        {{ $customer->shopname ? $customer->shopname : '-' }}<br>
                                                        Phone: {{ $customer->phone }}<br>
                                                        Email: {{ $customer->email }}<br>
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="mb-3">Ringkasan Pesanan</h5>
                                <div class="table-responsive-lg">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" scope="col">#</th>
                                                <th scope="col">Produk</th>
                                                <th class="text-center" scope="col">Jumlah</th>
                                                <th class="text-center" scope="col">Harga</th>
                                                <th class="text-center" scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productItem->content() as $item)
                                                <tr>
                                                    <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                    <td>
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                    </td>
                                                    <td class="text-center">{{ $item->qty }}</td>
                                                    <td class="text-center">Rp
                                                        {{ number_format($item->price, 0, ',', '.') }}</td>
                                                    <td class="text-center"><b>Rp
                                                            {{ number_format($item->subtotal, 0, ',', '.') }}</b></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 mb-3">
                            <div class="offset-lg-8 col-lg-4">
                                <div class="or-detail rounded">
                                    <div class="p-3">
                                        <h5 class="mb-3">Rincian Pesanan</h5>
                                        {{-- <div class="mb-2">
                                            <h6>Sub Total</h6>
                                            <p>Rp {{ number_format($productItem->subtotal(), 0, ',', '.') }}</p>
                                        </div>
                                        <div>
                                            <h6>PPN (5%)</h6>
                                            <p>Rp {{ number_format($productItem->tax(), 0, ',', '.') }}</p>
                                        </div> --}}
                                    </div>
                                    <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                        <h6>Total</h6>
                                        <h3 class="text-primary font-weight-700">Rp
                                            {{ number_format($productItem->total(), 0, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
