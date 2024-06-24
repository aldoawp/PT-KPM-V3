@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Laporan Penjualan</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('report.salesReport') }}" method="POST">
                            @csrf
                            <!-- begin: Input Data -->
                            <div class="row align-items-center">
                                <div class="form-group col-md-4">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required autocomplete="off">
                                    @error('start_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="end_date">Tanggal Akhir</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required autocomplete="off">
                                    @error('end_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="region">Pilih Daerah</label>
                                    <select class="form-control @error('region') is-invalid @enderror" id="region" name="region" required>
                                        <option value="">Semua Daerah</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ Str::title($branch->region) }}</option>
                                        @endforeach
                                    </select>
                                    @error('region')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- end: Input Data -->
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Download Laporan (.XLS / .XLSX)</button>
                                <a class="btn bg-danger" href="{{ route('dashboard') }}">Kembali ke Dashboard</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
