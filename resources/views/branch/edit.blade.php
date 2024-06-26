@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Tambah Daerah Operasional</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('branch.update', $branch->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <!-- begin: Input Data -->
                            <div class=" row align-items-center">
                                <div class="form-group col-md-6">
                                    <label for="region">Nama Daerah <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('region') is-invalid @enderror"
                                        id="region" name="region" value="{{ old('region', $branch->region) }}" required
                                        autocomplete="off">
                                    @error('region')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="address">Alamat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                        id="address" name="address" value="{{ old('address', $branch->address) }}" required
                                        autocomplete="off">
                                    @error('address')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- end: Input Data -->
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                <a class="btn bg-danger" href="{{ route('branch.index') }}">Batalkan</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
