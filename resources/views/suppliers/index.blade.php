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
                        <h4 class="mb-3">Daftar Pemasok</h4>
                        <p class="mb-0">Semua pemasok anda dari berbagai daerah akan disimpan pada halaman ini.</p>
                    </div>
                    <div>
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary add-list"><i
                                class="fa-solid fa-plus mr-3"></i>Tambah Pemasok</a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-danger add-list"><i
                                class="fa-solid fa-trash mr-3"></i>Hapus Pencarian</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('suppliers.index') }}" method="get">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="form-group row">
                            <label for="row" class="col-sm-3 align-self-center">Baris:</label>
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

                        <div class="form-group ml-auto">
                            <div class="input-group">
                                <input type="text" id="search" class="form-control" name="search"
                                    placeholder="Cari pemasok..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary"><i
                                            class="fa-solid fa-magnifying-glass font-size-20"></i></button>
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
                                <th>Foto</th>
                                <th>@sortablelink('name', 'nama')</th>
                                <th>@sortablelink('email')</th>
                                <th>@sortablelink('phone', 'Np HP')</th>
                                <th>@sortablelink('shopname', 'Nama Usaha')</th>
                                <th>@sortablelink('type', 'Tipe')</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @foreach ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $suppliers->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $supplier->photo ? asset('storage/suppliers/' . $supplier->photo) : asset('assets/images/user/1.png') }}">
                                    </td>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->shopname }}</td>
                                    <td>{{ $supplier->type }}</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge badge-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Tampilkan"
                                                href="{{ route('suppliers.show', $supplier->id) }}"><i
                                                    class="ri-eye-line mr-0"></i>
                                            </a>
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Ubah"
                                                href="{{ route('suppliers.edit', $supplier->id) }}""><i
                                                    class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST"
                                                style="margin-bottom: 5px">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="badge bg-warning mr-2 border-none"
                                                    onclick="return confirm('Are you sure you want to delete this record?')"
                                                    data-toggle="tooltip" data-placement="top" title=""
                                                    data-original-title="Hapus"><i
                                                        class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $suppliers->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
