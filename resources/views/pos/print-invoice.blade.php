<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>PT KPM | Print Invoice</title>

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}"/>
        <link rel="stylesheet" href="{{ asset('assets/css/backend-plugin.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/backend.css?v=1.0.0') }}">
        <link href="https://cdn.jsdelivr.net/gh/hung1001/font-awesome-pro@4cac1a6/css/all.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/remixicon/fonts/remixicon.css') }}">
    </head>
<body>

    <!-- Wrapper Start -->
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-block">
                        <div class="card-header d-flex justify-content-between bg-primary">
                            <div class="iq-header-title">
                                <h4 class="card-title mb-0">{{ $order->invoice_no }}</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <img src="{{ asset('assets/images/logo.png') }}" class="logo-invoice img-fluid mb-3">
                                    <h5 class="mb-3">Hello, {{ $customer->name }}</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive-sm">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Tanggal Pesanan</th>
                                                    <th scope="col">Status Pesanan</th>
                                                    <th scope="col">No Invoice</th>
                                                    <th scope="col">Alamat Tagihan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Jan 17, 2016</td>
                                                    <td><span class="badge badge-danger">Unpaid</span></td>
                                                    <td>250028</td>
                                                    <td>
                                                        <p class="mb-0">{{ $customer->address }}<br>
                                                            Shop Name: {{ $customer->shopname ? $customer->shopname : '-' }}<br>
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
                                    <h5 class="mb-3">Ringkasan Order</h5>
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
                                                @foreach ($content as $item)
                                                <tr>
                                                    <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                                    <td>
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                    </td>
                                                    <td class="text-center">{{ $item->qty }}</td>
                                                    <td class="text-center">{{ $item->price }}</td>
                                                    <td class="text-center"><b>{{ $item->subtotal }}</b></td>
                                                </tr>

                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <b class="text-danger">Catatan:</b>
                                    <p class="mb-0">It is a long established fact that a reader will be distracted by the readable content of a page
                                        when looking
                                        at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters,
                                        as opposed to using 'Content here, content here', making it look like readable English.</p>
                                </div>
                            </div>
                            <div class="row mt-4 mb-3">
                                <div class="offset-lg-8 col-lg-4">
                                    <div class="or-detail rounded">
                                        <div class="p-3">
                                            <h5 class="mb-3">Rincian Pesanan</h5>
                                            <div class="mb-2">
                                                <h6>Bank</h6>
                                                <p>{{ $customer->bank_name }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <h6>No. Rekening</h6>
                                                <p>{{ $customer->account_number }}</p>
                                            </div>
                                            <div class="mb-2">
                                                <h6>Jatuh Tempo</h6>
                                                <p>12 August 2020</p>
                                            </div>
                                            <div class="mb-2">
                                                <h6>Sub Total</h6>
                                                <p>Rp {{ number_format(Cart::subtotal(), 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <h6>Vat (5%)</h6>
                                                <p>Rp {{ number_format(Cart::tax(), 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                            <h6>Total</h6>
                                            <h3 class="text-primary font-weight-700">Rp {{ number_format(Cart::total(), 0, ',', '.') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Wrapper End-->

    <script>
    window.addEventListener("load", (event) => {
        window.print();
    });
    </script>
</body>
</html>
