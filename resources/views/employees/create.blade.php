@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Tambah Karyawan</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <!-- begin: Input Image -->
                            <div class="form-group row align-items-center">
                                <div class="col-md-12">
                                    <div class="profile-img-edit">
                                        <div class="crm-profile-img-edit">
                                            <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview"
                                                src="{{ asset('assets/images/user/1.png') }}" alt="profile-pic">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="input-group mb-4 col-lg-6">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('photo') is-invalid @enderror"
                                            id="image" name="photo" accept="image/*" onchange="previewImage();">
                                        <label class="custom-file-label" for="photo">Choose file</label>
                                    </div>
                                    @error('photo')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- end: Input Image -->
                            <!-- begin: Input Data -->
                            <div class=" row align-items-center">
                                <div class="form-group col-md-12">
                                    <label for="name">Nama Karyawan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="email">Email Karyawan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="phone">No HP Karyawan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                        id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="experience">Pengalaman Karyawan</label>
                                    <select class="form-control" name="experience">
                                        <option value="">Pilih Tahun...</option>
                                        <option value="> 1 Tahun">&lt 1 Tahun</option>
                                        <option value="2 Tahun">2 Tahun</option>
                                        <option value="3 Tahun">3 Tahun</option>
                                        <option value="4 Tahun">4 Tahun</option>
                                        <option value="< 5 Tahun">&gt 5 Tahun</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="salary">Gaji Karyawan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('salary') is-invalid @enderror"
                                        id="salary" name="salary" value="{{ old('salary') }}" required>
                                    @error('salary')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="vacation">Jatah Cuti</label>
                                    <input type="text" class="form-control @error('vacation') is-invalid @enderror"
                                        id="vacation" name="vacation" value="{{ old('vacation') }}">
                                    @error('vacation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="branch_id_employee">Kota Karyawan <span class="text-danger">*</span></label>
                                    <select id="branch_id_employee" class="form-control" name="branch_id" required>
                                        <option value="" selected disabled>Pilih Kota...</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ Str::title($branch->region) }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="address">Alamat Karyawan<span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" name="address" required>{{ old('address') }}</textarea>
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
                                <a class="btn bg-danger" href="{{ route('employees.index') }}">Batalkan</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Page end  -->
    </div>

    @include('components.preview-img-form')
@endsection
