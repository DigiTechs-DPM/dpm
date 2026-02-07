<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}">

    <title>DPM CRM | Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- fonts  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
        integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('user-assets') }}/css/custom.css">
    <link rel="stylesheet" href="{{ asset('user-assets') }}/css/responsive.css">

</head>

<body>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"
        integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>

    <script>
        $(document).ready(function() {
            toastr.options.timeOut = 10000;

            @if (Session::has('success'))
                toastr.success('{{ Session::get('success') }}');
            @endif

            @if (Session::has('info'))
                toastr.info('{{ Session::get('info') }}');
            @endif

            @if (Session::has('warning'))
                toastr.warning('{{ Session::get('warning') }}');
            @endif

            @if (Session::has('error'))
                toastr.error('{{ Session::get('error') }}');
            @endif
        });
    </script>
    <section class="sign-in-sec">
        <div class="container-fluid p-0">
            <div class="row align-items-center justify-content-center sign-up-row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 p-0 right-content bg-dark">
                    <div class="sign-in-left sign-up-left">
                        <div class="logo-container">
                            <img src="{{ url('admin-assets/dpm-logos/4.png') }}" class="img-fluid" alt="">
                        </div>
                        <div class="log-in-content">
                            <h1>Log in to your account</h1>
                            <p>Please enter your information here to view details</p>
                        </div>
                        <div class="log-in-foam">
                            <form class="form-horizontal form-simple" action="{{ route('client.reset.post') }}"
                                method="post">
                                @csrf
                                <input type="hidden" class="form-control " name="token" value="{{ $token }}">
                                <div class="cridentials">
                                    <label for="exampleInputEmail1" class="form-label">E-Email</label>
                                    <input type="email" class="form-control " name="email"
                                        placeholder="Enter your email address" value="" autofocus
                                        id="exampleInputEmail1" aria-describedby="emailHelp">
                                    <span class="icons">
                                        <img src="{{ asset('user-assets') }}/images/icon-1.png" class="img-fluid"
                                            alt="">
                                    </span>
                                </div>
                                <div class="cridentials">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control "
                                        placeholder="Enter your password..." id="exampleInputPassword1">
                                    <span class="icons">
                                        <img src="{{ asset('user-assets') }}/images/icon-2.png" class="img-fluid"
                                            alt="">
                                    </span>
                                </div>
                                <div class="cridentials">
                                    <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
                                    <input type="password" name="cpassword" class="form-control "
                                        placeholder="Enter password again... " id="exampleInputPassword1">
                                    <span class="icons">
                                        <img src="{{ asset('user-assets') }}/images/icon-2.png" class="img-fluid"
                                            alt="">
                                    </span>
                                </div>
                                <button type="submit" class="btn custom-btn-login">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 p-0 slider-main-colum"
                    id="hide-on-mobile">
                    <div class="slider-main"
                        style="background-image: url('https://designcrm.net/images/crm-main.jpg') !important;">
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script>
        var selector = '.main-id-date p';

        $(selector).on('click', function() {
            $(selector).removeClass('active');
            $(this).addClass('active');
        });
    </script>


</body>

</html>
