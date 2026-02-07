<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google-site-verification" content="ztvhppa4266dY22ykOfFHz9Q7KMt_Mth3-UI6VWWwcU" />
    <link href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}" rel="icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('admin-assets/dpm-logos/dpm-fav.png') }}">

    <title>@yield('title')</title>
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
    <link rel="stylesheet" href="{{ url('user-assets') }}/css/custom.css">
    <link rel="stylesheet" href="{{ url('user-assets') }}/css/responsive.css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />
    <link href="{{ url('user-assets') }}/css/toaster.css" rel="stylesheet" />
    <link href="{{ url('user-assets') }}/css/datatables.min.css" rel="stylesheet" />
    <link href="{{ url('user-assets') }}/css/select.min.css" rel="stylesheet" />
    <link href="{{ url('user-assets') }}/css/sweetalert2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/introjs.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
         :root {
            /* Logo and favicon URLs */
            --brand-favicon: url('{{ config('branding.fav-icon') }}');
            --brand-logo: url('{{ config('branding.logo') }}');

            /* Gradient */
            --bg-gradient: {{ config('branding.bg-gradient') }};

            /* Colors */
            --color-primary: {{ config('branding.colors.primary') }};
            --color-secondary: {{ config('branding.colors.secondary') }};
            --color-success: {{ config('branding.colors.success') }};
            --color-danger: {{ config('branding.colors.danger') }};
            --color-warning: {{ config('branding.colors.warning') }};
            --color-info: {{ config('branding.colors.info') }};
            --color-white: {{ config('branding.colors.white') }};
        }
        .introjs-tooltip-header {
            padding: 0px;
        }

        .introjs-tooltiptext {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 18px;
        }

        .introjs-bullets {
            padding: 0px;
        }

        a.introjs-button {
            padding: 2%;
        }

        #profile_image {
            border-radius: 100%;
            border: 2px solid #01abea;
            height: 46px;
            width: 46px;
            object-fit: cover;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #db165b, #673187, #f7b63e);
            color: white !important;
        }

        .description_para {
            height: 60px !important;
        }

        #profile_image {
            border-radius: 100%;
            border: 2px solid #01abea;
            height: 46px;
            min-height: 46px;
            width: 46px;
            min-width: 46px;
            object-fit: cover;
        }

        .file-wrapper {
            max-width: 270px;
        }
    </style>

</head>

<div class="modal fade" id="modal_file_size" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Error uploading file(s)</h5>
                <button type="button" class="close btn_close_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    The file you are trying to upload exceeds the maximum allowed size of 100MB. Please select a smaller
                    file.
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn_close_modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<body>

    @include('clients.includes.header')

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

    @yield('mian-content')


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

    <script>
        // Wait for the DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('redirectButton');

            if (button) {
                button.addEventListener('click', function() {
                    const currentPath = window.location.pathname;

                    // Check if we're on 'index.php' (or root page)
                    if (currentPath === '/index.php' || currentPath === '/' || currentPath.endsWith(
                            '/index.php')) {
                        // Redirect to 'messages.php'
                        window.location.href = "messages.php";
                    }
                    // Check if we're on 'messages.php'
                    else if (currentPath === '/messages.php' || currentPath.endsWith('/messages.php')) {
                        // Redirect to 'index.php'
                        window.location.href = "index.php";
                    }
                });
            }
        });
    </script>


</body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>

<script>
    // const rand = () =>
    //     Array.from({length: 10}, () => Math.floor(Math.random() * 100));
    //
    // // let data = rand();
    // const checkingData = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, .0, 0.0];
    // const savingsData = [300.27, 500.27, 150.27, 430.27, 170.27, 287.27, 100.27, 287.27, 500.27, 245.27];

    // function addData(chart, data) {
    //   chart.data.datasets.forEach(dataset => {
    //     let data = dataset.data;
    //     const first = data.shift();
    //     data.push(first);
    //     dataset.data = data;
    //   });

    //   chart.update();
    // }
</script>


<script src="{{ url('user-assets') }}/js/bootstrap.bundle.js"></script>
<script src="{{ url('user-assets') }}/js/perfectscrollbar.min.js"></script>
<script src="{{ url('user-assets') }}/js/script.min.js"></script>
<script src="{{ url('user-assets') }}/js/sidebar.large.script.min.js"></script>
<script src="{{ url('user-assets') }}/js/echart.min.js"></script>
{{-- <script src="{{ url('user-assets') }}/js/echart.options.min.js"></script> --}}
<script src="{{ url('user-assets') }}/js/datatables.min.js"></script>
<script src="{{ url('user-assets') }}/js/toastr.min.js"></script>
<script src="{{ url('user-assets') }}/js/select2.min.js"></script>
<script src="{{ url('user-assets') }}/js/Chart.min.js"></script>
<script src="{{ url('user-assets') }}/js/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intro.js/7.2.0/intro.min.js" crossorigin="anonymous"
    referrerpolicy="no-referrer"></script>
<script>
    introJs().setOption("dontShowAgain", true).start();
</script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script>
    $(document).ready(() => {
        //global vars
        let auth_id = parseInt('4433');

        // Enable Pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('7d1bc788fe2aaa7a2ea5', {
            cluster: 'ap2'
        });

        var channel = pusher.subscribe('message-channel-for-client-user-' + auth_id.toString());
        channel.bind('new-message', function(data) {
            swal({
                icon: 'info',
                title: data.text,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: "View message",
            }).then((result) => {
                if (result && data.redirect_url) {
                    window.location.href = data.redirect_url
                }
            });

            console.log(data);
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const rand = () =>
        Array.from({
            length: 10
        }, () => Math.floor(Math.random() * 100));

    // let data = rand();
    const checkingData = [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, .0, 0.0];
    const savingsData = [300.27, 500.27, 150.27, 430.27, 170.27, 287.27, 100.27, 287.27, 500.27, 245.27];

    // function addData(chart, data) {
    //   chart.data.datasets.forEach(dataset => {
    //     let data = dataset.data;
    //     const first = data.shift();
    //     data.push(first);
    //     dataset.data = data;
    //   });

    //   chart.update();
    // }

    var ctx = document.getElementById("myChart").getContext("2d");
    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["In progress", "On Hold", "Completed"],
            datasets: [{
                axis: 'y',
                // data: [65, 59, 80], // Match the data length to labels
                data: JSON.parse('[0,0,0]'), // Match the data length to labels
                backgroundColor: [
                    'rgba(173, 216, 230, 0.5)', // Proper RGBA format
                    'rgba(0, 0, 139, 0.5)',
                    'rgba(40, 167, 69, 0.5)',
                ],
                borderColor: [
                    'rgb(173, 216, 230)',
                    'rgb(0, 0, 139)',
                    'rgb(40, 167, 69)',
                ],
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal bars,
            barThickness: 40,
            animation: {
                duration: 250
            },
            plugins: {
                legend: {
                    display: false // Removes the top-center label
                },
                title: {
                    display: false,
                    text: "Service progress" // Chart title
                }
            }
        }
    });
</script>

<script>
    $(document).ready(function() {
        var chatContainer = $('.conversions');
        chatContainer.scrollTop(chatContainer.prop('scrollHeight'));

        $('.btn_close_modal').on('click', function() {
            $('#modal_file_size').modal('hide');
        });

        $('#file-upload').on('change', function() {
            // toastr.success('File attached');

            var files = this.files;
            var maxSize = 100000 * 1024; // 100000KB in bytes
            var isValid = true;

            for (var i = 0; i < files.length; i++) {
                if (files[i].size > maxSize) {
                    // alert('Error: ' + files[i].name + ' exceeds 100KB size limit.');
                    isValid = false;
                    break; // Stop checking further if one file exceeds the limit
                }
            }

            if (isValid) {
                toastr.success('File attached');
            } else {
                // $(this).val(''); // Clear file input if there's an invalid file

                //reset form code here

                $('.form')[0].reset();
                $('#modal_file_size').modal('show');
                return false;
            }
        });

        $('.form').on('submit', function(e) {
            $('#loading-screen').show();
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.text-area p').forEach((p) => {
            if (!p.textContent.trim()) {
                p.remove();
            }
        });

        document.querySelectorAll('.text-area br').forEach((br) => {
            if (!br.textContent.trim()) {
                br.remove();
            }
        });
    });
</script>


<script></script>
