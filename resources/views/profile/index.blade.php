@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    @include('profile.partials.background-profile')

    <div class="row px-3">
        @include('profile.partials.left-profile')

        <div class="col-lg-8 card-profile">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <!-- begin: Profile -->
                    @include('profile.partials.show-profile')
                    <!-- end: Profile -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
