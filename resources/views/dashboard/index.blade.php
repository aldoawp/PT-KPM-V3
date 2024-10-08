@extends('dashboard.body.main')

{{-- Chart pendapatan & pendapatan per lokasi --}}

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
            </div>
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Halo {{ auth()->user()->name }},</h3>
                        <p class="mb-0 mr-4">Semoga hari ini menjadi hari yang penuh berkah dan kebahagiaan. Selamat beraktifitas!</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-info-light">
                                        <img src="../assets/images/product/1.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Pendapatan Harian</p>
                                        <h4 class="text-truncate" style="max-width: 150px">Rp {{ number_format($today_income, 0, ',', '.') }}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-info iq-progress progress-1" data-percent="85">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-danger-light">
                                        <img src="../assets/images/product/2.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Produk Terjual Harian</p>
                                        <h4>{{ number_format($today_product, 0, ',', '.') }} pcs</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-danger iq-progress progress-1" data-percent="70">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="card card-block card-stretch card-height">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4 card-total-sale">
                                    <div class="icon iq-icon-box-2 bg-success-light">
                                        <img src="../assets/images/product/3.png" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <p class="mb-2">Pesanan Selesai Harian</p>
                                        <h4>{{ count($today_complete_orders) }}</h4>
                                    </div>
                                </div>
                                <div class="iq-progress-bar mt-2">
                                    <span class="bg-success iq-progress progress-1" data-percent="75">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Total Pendapatan</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton001">
                                    Bulan Ini</i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton001">
                                    <a class="dropdown-item" href="#">Tahunan</a>
                                    <a class="dropdown-item" href="#">Bulanan</a>
                                    <a class="dropdown-item" href="#">Mingguan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart1" data-income-weekly='@json($income_weekly)'
                            data-income-total='@json($income_total)'></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Pendapatan per Lokasi</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton002">
                                    Bulan Ini</i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton002">
                                    <a class="dropdown-item" href="#">Tahunan</a>
                                    <a class="dropdown-item" href="#">Bulanan</a>
                                    <a class="dropdown-item" href="#">Mingguan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="layout1-chart-2" style="min-height: 360px;" data-income-location='@json($income_per_location)' data-empty='{{ $income_per_location->isEmpty() ? 'true' : 'false' }}'></div>
                        <div id="no-data-message" style="display: none; font-size: 24px; text-align: center; color: #e5e5e5; text-transform: uppercase; margin-top:20%; margin-bottom: 20%">Belum ada data</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Produk Paling Laku</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton006">
                                    Bulan Ini</i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton006">
                                    <a class="dropdown-item" href="#">Tahunan</a>
                                    <a class="dropdown-item" href="#">Bulanan</a>
                                    <a class="dropdown-item" href="#">Mingguan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if ($best_sellers->isEmpty())
                            <div id="no-data-message-1" style="font-size: 24px; text-align: center; color: #e5e5e5; text-transform: uppercase; margin-top:10%; margin-bottom: 10%">Belum ada data</div>
                        @else
                            <ul class="list-unstyled row top-product mb-0">
                                @foreach ($best_sellers as $product)
                                    <li class="col-lg-3">
                                        <div class="card card-block card-stretch card-height mb-0">
                                            <div class="card-body">
                                                <div class="bg-warning-light rounded">
                                                    <img src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}"
                                                        class="style-img img-fluid m-auto p-3" alt="image">
                                                </div>
                                                <div class="style-text text-left mt-3">
                                                    <h5 class="mb-1">{{ $product->product_name }}</h5>
                                                    <p class="mb-0">{{ $product->total_quantity }} pcs</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                                @if ($best_sellers->count() > 0 && $best_sellers->count() <= 3)
                                    @for ($i = $best_sellers->count(); $i <= 3; $i++)
                                        <li class="col-lg-3">
                                            <div class="card card-block card-stretch card-height mb-0 d-none">
                                                <div class="card-body">
                                                    <div class="bg-warning-light rounded">
                                                        <img src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/images/product/default.webp') }}"
                                                            class="style-img img-fluid m-auto p-3" alt="image">
                                                    </div>
                                                    <div class="style-text text-left mt-3">
                                                        <h5 class="mb-1">{{ $product->product_name }}</h5>
                                                        <p class="mb-0">{{ $product->total_quantity }} pcs</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endfor
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between p-0">
                        <div class="header-title">
                            <h4 class="card-title mb-0">Top Sales</h4>
                        </div>
                        <div class="card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                                <span class="dropdown-toggle dropdown-bg btn" id="dropdownMenuButton006">
                                    Bulan Ini</i>
                                </span>
                                <div class="dropdown-menu dropdown-menu-right shadow-none"
                                    aria-labelledby="dropdownMenuButton006">
                                    <a class="dropdown-item" href="#">Tahunan</a>
                                    <a class="dropdown-item" href="#">Bulanan</a>
                                    <a class="dropdown-item" href="#">Mingguan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Sales Section --}}
                <div class="card-container border-bottom ">
                    @if ($top_sales->isEmpty())
                        <div id="no-data-message-1" style="font-size: 24px; text-align: center; color: #e5e5e5; text-transform: uppercase; margin-top:20%; margin-bottom: 20%">Belum ada data</div>
                    @else
                        @foreach ($top_sales as $index => $sales)
                        <div class="card card-block card-stretch card-height-helf mb-3">
                            <div class="card-body card-item-right">
                                <div class="d-flex align-items-top">
                                    <div class="bg-warning-light rounded">
                                        <img src="../assets/images/product/04.png" class="style-img img-fluid m-auto" 
                                            alt="image">
                                    </div>
                                    <div class="style-text text-left ml-3">
                                        <h5 class="mb-2"><span class="mr-2 fw-bold bg-primary px-2 py-1 rounded-circle small">
                                            {{ $index + 1 }}</span>{{ $sales->name }}</h5>
                                        @if (auth()->user()->hasRole('SuperAdmin') || auth()->user()->hasRole('Owner'))
                                            <p class="mb-2"><span class="badge badge-pill badge-success">Total
                                                Pendapatan:</span><br><strong>Rp
                                                {{ number_format($sales->total_sales, 0, ',', '.') }}</strong></p>
                                            <p class="mb-0"><span class="badge badge-pill badge-success">Total Produk
                                                    Terjual:</span><br><strong>{{ $sales->total_products }}</strong></p>
                                        @endif

                                        @if (auth()->user()->hasRole('ASS'))
                                            @if ($index + 1 == 1)
                                                <div>Selamat! Kamu memiliki performa yang paling baik diantara rekanmu.</div>
                                            @endif

                                            @if ($index + 1 == 2)
                                                <div>Bagus! Kamu di peringkat kedua. Terus kejar posisi teratas!</div>
                                            @endif

                                            @if ($index + 1 == 3)
                                                <div>Hebat! Kamu di peringkat ketiga. Tetap semangat!</div>
                                            @endif

                                            @if ($index + 1 > 3)
                                                <div>Tetap semangat! Tingkatkan lagi performamu!</div>
                                            @endif
                                        @endif
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endif
                    
                </div>

                {{-- Top sales: shown 2 cards only at a time, the rest is scrolled --}}
                <style>
                    .card-container {
                        max-height: 370px;
                        /* Adjust this value as needed */
                        overflow-y: auto;
                        padding-right: 10px;
                        margin-bottom: 30px;
                    }

                    /* Optionally, add some styling for the scrollbar */
                    .card-container::-webkit-scrollbar {
                        width: 6px;
                    }

                    .card-container::-webkit-scrollbar-thumb {
                        background-color: rgba(0, 0, 0, 0.2);
                        border-radius: 4px;
                    }
                </style>

            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection

@section('specificpagescripts')
    <!-- Table Treeview JavaScript -->
    <script src="{{ asset('assets/js/table-treeview.js') }}"></script>
    <!-- Chart Custom JavaScript -->
    <script src="{{ asset('assets/js/customizer.js') }}"></script>
    <!-- Chart Custom JavaScript -->
    <script async src="{{ asset('assets/js/chart-custom.js') }}"></script>
@endsection
