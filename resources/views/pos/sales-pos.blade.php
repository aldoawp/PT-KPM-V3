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
                @if (session()->has('warning'))
                    <div class="alert alert-danger" role="alert">
                        <div class="iq-alert-text">{{ session('warning') }}</div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <i class="ri-close-line text-danger"></i>
                        </button>
                    </div>
                @endif
                <div>
                    <h4 class="mb-3">Transaksi Penjualan</h4>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 mb-3">
                <table class="table">
                    <thead>
                        <tr class="ligth">
                            <th scope="col">Nama</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Harga</th>
                            <th scope="col">SubTotal</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productItem->content() as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="min-width: 140px;">
                                    <form action="{{ route('pos.sales.updateCart', $item->rowId) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="qty" required
                                                value="{{ old('qty', $item->qty) }}" min="1"
                                                max="{{ \App\Models\Product::find($item->id)->product_store }}">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-success border-none"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Sumbit"><i class="fas fa-check"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('pos.sales.deleteCart', $item->rowId) }}"
                                        class="btn btn-danger border-none" data-toggle="tooltip" data-placement="top"
                                        title="" data-original-title="Delete"><i
                                            class="fa-solid fa-trash mr-0"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="container text-center">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="form-group cart-info">
                                <p class="h6">Jumlah:</p>
                                <p class="h5">{{ $productItem->count() }}</p>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group cart-info">
                                <p class="h6">Subtotal:</p>
                                <p class="h5">Rp {{ number_format($productItem->subtotal(), 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group cart-info">
                                <p class="h6">PPN (5%):</p>
                                <p class="h5">Rp {{ number_format($productItem->tax(), 0, ',', '.') }}</p>
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group cart-info">
                                <p class="h6">Total:</p>
                                <p class="h5">Rp {{ number_format($productItem->total(), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .cart-info {
                        background-color: #f8f9fa;
                        /* Light background */
                        padding: 20px;
                        border-radius: 10px;
                        /* Rounded borders */
                        border: 1px solid #ddd;
                        /* Light border */
                        margin: 10px 0;
                        /* Spacing between elements */
                    }

                    .cart-info p {
                        color: #000;
                        /* Text color black */
                    }
                </style>

                <form action="{{ route('pos.createReceipt') }}" method="POST">
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="input-group">
                                <select class="form-control" id="customer_id" name="customer_id" role="button">
                                    <option selected="" disabled="">-- Pilih Pelanggan --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @error('error')
                            <div class="col-md-12">
                                <span class="alert alert-danger mt-2">{{ $errors->first('error') }}</span>
                            </div>
                        @enderror
                        <div class="col-md-12 mt-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-center">
                                <a href="{{ route('customers.create', ['previous_url' => url()->current()]) }}"
                                    class="btn btn-primary add-list mx-1">Tambah
                                    Pelanggan</a>
                                <button type="submit" class="btn btn-success add-list mx-1">Buat Transaksi</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6 col-md-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <form action="#" method="get">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <div class="form-group row">
                                    <label for="row" class="align-self-center mx-2">Baris:</label>
                                    <div>
                                        <select class="form-control" name="row">
                                            <option value="10"
                                                @if (request('row') == '10') selected="selected" @endif>10</option>
                                            <option value="25"
                                                @if (request('row') == '25') selected="selected" @endif>25</option>
                                            <option value="50"
                                                @if (request('row') == '50') selected="selected" @endif>50</option>
                                            <option value="100"
                                                @if (request('row') == '100') selected="selected" @endif>100</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="input-group">
                                        <input type="text" id="search" class="form-control" name="search"
                                            placeholder="Cari produk..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i
                                                    class="fa-solid fa-magnifying-glass font-size-20"></i></button>
                                            <a href="{{ route('products.index') }}" class="input-group-text bg-danger"><i
                                                    class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive rounded mb-3 border-none">
                            <table class="table mb-0">
                                <thead class="bg-white text-uppercase">
                                    <tr class="ligth ligth-data">
                                        <th>Foto</th>
                                        <th>@sortablelink('product_name', 'Nama')</th>
                                        <th>@sortablelink('selling_price', 'Harga')</th>
                                        <th>Stok</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody class="ligth-body">
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                <img class="avatar-60 rounded"
                                                    src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}">
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>{{ 'Rp ' . number_format($product->selling_price, 0, ',', '.') }}</td>
                                            <td>{{ $product->product_store }}</td>
                                            <td>
                                                <form action="{{ route('pos.sales.addCart') }}" method="POST"
                                                    style="margin-bottom: 5px">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                                    <input type="hidden" name="name"
                                                        value="{{ $product->product_name }}">
                                                    <input type="hidden" name="price"
                                                        value="{{ $product->selling_price }}">

                                                    <button type="submit" class="btn btn-primary border-none"
                                                        data-toggle="tooltip" data-placement="top" title=""
                                                        data-original-title="Add"><i
                                                            class="far fa-plus mr-0"></i></button>
                                                </form>
                                            </td>
                                        </tr>

                                    @empty
                                        <div class="alert text-white bg-danger" role="alert">
                                            <div class="iq-alert-text">Data not Found.</div>
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </div>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
