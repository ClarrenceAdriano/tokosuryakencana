@extends('layouts.app')

@section('title', 'Profile')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

            <h2 class="fw-bold mb-4 text-dark">{{ __('Profile') }}</h2>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-danger">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
@endsection
