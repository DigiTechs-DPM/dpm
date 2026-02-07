@extends('clients.layouts.layout')

@section('title', 'CRM | Profile')

@section('mian-content')

    <section class="profile-section">
        <div class="container bg-colored">
            <form action="{{ route('auth.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-center">
                    <div class="col-lg-4">
                        <div class="profile-left-parent doted-border">
                            <p class="heading-3">
                                Profile Picture
                            </p>
                            <figure class="large-profile">
                                @if (!$profile)
                                    <img src="https://designcrm.net/images/avatar.png" class="img-fluid" id="preview_image"
                                        alt="">
                                @else
                                    <img src="{{ asset('uploads/profiles/' . $profile->profile) }}" class="img-fluid"
                                        id="preview_image" alt="" style="width: 350px; height: 300px">
                                @endif
                            </figure>
                            <span class="upload-profile-pic">
                                <label for="file-upload" class="custom-file-upload d-flex align-items-center">
                                    <figure class="mx-2">
                                        <img src="https://designcrm.net/images/upload-icon.png" class="img-fluid"
                                            alt="">
                                    </figure>
                                    <br>
                                    <p>
                                        Click to upload
                                    </p>
                                </label>
                                <input id="file-upload" type="file" name="profile" accept=".jpeg, .jpg, .png, .webp">
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="profile-right-parent">
                            <p class="heading-3">
                                Profile Details
                            </p>
                            <div class="parent-profile-details">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                value="{{ old('first_name', $firstName ?? '') }}" placeholder="Jason">
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email"
                                                value="{{ old('email', $user->email ?? '') }}" disabled readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone', $profile->phone ?? '') }}"
                                                placeholder="+1 987 654 321">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                value="{{ old('last_name', $lastName ?? '') }}" placeholder="Martin">
                                        </div>

                                        <div class="mb-3">
                                            <label for="alternate_email" class="form-label">Alternative Email</label>
                                            <input type="email" class="form-control" id="alternate_email"
                                                name="alternate_email"
                                                value="{{ old('alternate_email', $profile->alternate_email ?? '') }}"
                                                placeholder="altemail@domain.com">
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ old('address', $profile->address ?? '') }}"
                                                placeholder="e.g. st# ***">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <h6>Change password</h6>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Enter new password">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <h6>&nbsp;</h6>
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm password</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" placeholder="Re-enter new password">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="profile-details-save-btn text-center">
                                            <button type="submit" class="btn btn-success custom-btn blue">
                                                Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection
