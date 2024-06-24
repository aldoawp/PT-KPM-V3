@extends('dashboard.body.main')

@section('container')
    <form action="{{ route('report.generate') }}" method="post">
        @csrf
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
@endsection
