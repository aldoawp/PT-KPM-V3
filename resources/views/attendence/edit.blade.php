@extends('dashboard.body.main')

@section('specificpagestyles')
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.14/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Ubah Absensi Karyawan</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('attendence.store') }}" method="POST">
                    @csrf
                        <!-- begin: Input Data -->
                        <div class="row align-items-center">
                            <div class="form-group col-md-6">
                                <label for="datepicker">Tanggal <span class="text-danger">*</span></label>
                                <input id="datepicker" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date', $date) }}" />
                                @error('date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-lg-12">
                                <div class="table-responsive rounded mb-3">
                                    <table class="table mb-0">
                                        <thead class="bg-white text-uppercase">
                                            <tr class="ligth ligth-data">
                                                <th>No.</th>
                                                <th>Karyawan</th>
                                                <th class="text-center">Status Kehadiran</th>
                                            </tr>
                                        </thead>
                                        <tbody class="ligth-body">
                                            @foreach ($attendences as $attendence)
                                            <tr>
                                                <td scope="row">{{ $key = $loop->iteration  }}</td>
                                                <td>{{ $attendence->employee->name}}</td>
                                                <td>
                                                    <input type="hidden" name="employee_id[{{ $key }}]" value="{{ $attendence->employee_id }}">
                                                    <div class="input-group">
                                                        <div class="input-group justify-content-center">
                                                            <div class="input-group-text">
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="present{{ $key }}" name="status{{ $key }}" class="custom-control-input position-relative" style="height: 20px" value="Hadir" {{ $attendence->status == 'Hadir' ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="present{{ $key }}"> Hadir </label>
                                                                </div>
                                                            </div>
                                                            <div class="input-group-text mx-2">
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="leave{{ $key }}" name="status{{ $key }}" class="custom-control-input position-relative" style="height: 20px" value="Ijin" {{ $attendence->status == 'Ijin' ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="leave{{ $key }}"> Ijin </label>
                                                                </div>
                                                            </div>
                                                            <div class="input-group-text">
                                                                <div class="custom-radio">
                                                                    <input type="radio" id="absent{{ $key }}" name="status{{ $key }}" class="custom-control-input position-relative" style="height: 20px" value="Tanpa Kabar" {{ $attendence->status == 'Tanpa Kabar' ? 'checked' : '' }}>
                                                                    <label class="custom-control-label" for="absent{{ $key }}"> Tanpa Kabar </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- end: Input Data -->
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary">Ubah</button>
                            <a href="{{ route('attendence.index') }}" class="btn btn-danger">Batalkan</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<script>
    $('#datepicker').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
        // https://gijgo.com/datetimepicker/configuration/format
    });
</script>
@endsection
