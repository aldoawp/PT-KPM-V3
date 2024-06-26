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
                        <h4 class="mb-3">Daftar Pembayaran Gaji</h4>
                        <p class="mb-0">Dasbor gaji gaji memungkinkan Anda dengan mudah mengumpulkan dan
                            memvisualisasikan<br>data gaji pembayaran dari mengoptimalkan
                            Pengalaman gaji pembayaran, memastikan <br> retensi gaji gaji. </p>
                    </div>
                    <div>
                        <a href="{{ route('advance-salary.create') }}" class="btn btn-primary add-list"><i
                                class="fas fa-plus mr-3"></i></i>Tambah Gaji</a>
                        <a href="{{ route('advance-salary.index') }}" class="btn btn-danger add-list"><i
                                class="fa-solid fa-trash mr-3"></i>Hapus Pencarian</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <form action="{{ route('pay-salary.index') }}" method="get">
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
                                    placeholder="Cari karyawan..." value="{{ request('search') }}">
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
                                <th>@sortablelink('employee.name', 'nama')</th>
                                <th>@sortablelink('date', 'tanggal')</th>
                                <th>@sortablelink('employee.salary', 'gaji')</th>
                                <th>@sortablelink('advance_salary', 'pinjaman')</th>
                                <th>Belum Dibayar</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="ligth-body">
                            @forelse ($advanceSalaries as $advanceSalary)
                                <tr>
                                    <td>{{ $advanceSalaries->currentPage() * 10 - 10 + $loop->iteration }}</td>
                                    <td>
                                        <img class="avatar-60 rounded"
                                            src="{{ $advanceSalary->employee->photo ? asset('storage/employees/' . $advanceSalary->employee->photo) : asset('assets/images/user/1.png') }}">
                                    </td>
                                    <td>{{ $advanceSalary->employee->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($advanceSalary->date)->format('M/Y') }}</td>
                                    <td>Rp {{ number_format($advanceSalary->employee->salary, 0, ',', '.') }}</td>
                                    <td>{{ $advanceSalary->advance_salary ? 'Rp ' . number_format($advanceSalary->advance_salary, 0, ',', '.') : 'Tidak Ada Pinjaman' }}
                                    </td>
                                    <td>Rp
                                        {{ number_format($advanceSalary->employee->salary - $advanceSalary->advance_salary, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Edit"
                                                href="{{ route('advance-salary.edit', $advanceSalary->id) }}""><i
                                                    class="ri-pencil-line mr-0"></i>
                                            </a>
                                            <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top"
                                                title="" data-original-title="Bayar"
                                                href="{{ route('pay-salary.paySalary', $advanceSalary->id) }}">Bayar</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Data not Found.</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $advanceSalaries->links() }}
            </div>
        </div>
        <!-- Page end  -->
    </div>
@endsection
