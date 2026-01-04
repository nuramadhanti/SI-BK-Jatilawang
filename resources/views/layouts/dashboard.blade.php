    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard </title>

        <link rel="shortcut icon" href="/logosmanja.png" type="image/x-icon">
        <link rel="shortcut icon" href="/logosmanja.png" type="image/png">
        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/app.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/app-dark.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/iconly.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/extensions/simple-datatables/style.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/table-datatable.css">

        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

        <link rel="stylesheet" href="{{ asset('../assets') }}/extensions/filepond/filepond.css">
        <link rel="stylesheet"
            href="{{ asset('../assets') }}/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/extensions/toastify-js/src/toastify.css">

        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/app.css">
        <link rel="stylesheet" href="{{ asset('../assets') }}/compiled/css/app-dark.css">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    </head>

    <body>
        <script src="{{ asset('../assets') }}/static/js/initTheme.js"></script>
        <div id="app">
            <div id="sidebar">
                <div class="sidebar-wrapper active">
                    <div class="sidebar-header position-relative">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="logo">
                                <a href="/"><img src="/logosmanja.png" class="pt-3"
                                        style="max-width: 100px; height: auto;" alt=""></a>
                            </div>
                            <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--system-uicons"
                                    width="20" height="20" preserveAspectRatio="xMidYMid meet"
                                    viewBox="0 0 21 21">
                                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                            opacity=".3"></path>
                                        <g transform="translate(-210 -1)">
                                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                            <circle cx="220.5" cy="11.5" r="4"></circle>
                                            <path
                                                d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2">
                                            </path>
                                        </g>
                                    </g>
                                </svg>
                                <div class="form-check form-switch fs-6">
                                    <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                        style="cursor: pointer">
                                    <label class="form-check-label"></label>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true" role="img" class="iconify iconify--mdi" width="20"
                                    height="20" preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                                    <path fill="currentColor"
                                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                                    </path>
                                </svg>
                            </div>
                            <div class="sidebar-toggler  x">
                                <a href="#" class="sidebar-hide d-xl-none d-block"><i
                                        class="bi bi-x bi-middle"></i></a>
                            </div>
                        </div>
                    </div>
                    @include('layouts.sidebar')
                </div>
            </div>

            <div id="main">
                <header class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-3">

                        <div class="dropdown">
                            <a class="nav-link position-relative" href="#" role="button"
                                id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-4"></i>
                                @php
                                    $unreadNotifications = auth()->user()->unreadNotifications()->count();
                                @endphp
                                @if ($unreadNotifications > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadNotifications }}
                                        <span class="visually-hidden">unread notifications</span>
                                    </span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown"
                                style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                    <strong>Notifikasi</strong>
                                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-link text-decoration-none">Tandai
                                            semua sebagai dibaca</button>
                                    </form>
                                </div>
                                <div class="list-group list-group-flush">
                                    @forelse (auth()->user()->notifications()->latest()->take(10)->get() as $notification)
                                        <a href="{{ route('permohonan-konseling.index') }}"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-start notif-item {{ is_null($notification->read_at) ? 'bg-light' : '' }}"
                                            data-id="{{ $notification->id }}">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold d-flex align-items-center gap-2">
                                                    @if ($notification->data['type'] === 'new')
                                                        <span class="badge bg-info">🔔 Baru</span>
                                                    @elseif ($notification->data['type'] === 'approved')
                                                        <span class="badge bg-success">✅ Disetujui</span>
                                                    @elseif ($notification->data['type'] === 'rejected')
                                                        <span class="badge bg-danger">❌ Ditolak</span>
                                                    @elseif ($notification->data['type'] === 'completed')
                                                        <span class="badge bg-primary">🎉 Selesai</span>
                                                    @endif
                                                    Permohonan Konseling
                                                </div>
                                                <small class="text-muted d-block">{{ $notification->data['message'] }}</small>
                                                
                                                {{-- Enhanced Priority 1 Data --}}
                                                @if (!empty($notification->data['skor_prioritas']))
                                                    <small class="d-block text-muted mt-1">
                                                        📊 Skor: <strong>{{ $notification->data['skor_prioritas'] }}</strong>
                                                    </small>
                                                @endif
                                                
                                                @if (!empty($notification->data['kategori_masalah']))
                                                    <small class="d-block text-muted">
                                                        🏷️ Kategori: <strong>{{ $notification->data['kategori_masalah'] }}</strong>
                                                    </small>
                                                @endif
                                                
                                                @if ($notification->data['type'] === 'rejected' && !empty($notification->data['alasan_penolakan']))
                                                    <small class="d-block text-danger mt-1">
                                                        <strong>📝 Alasan:</strong> {{ substr($notification->data['alasan_penolakan'], 0, 50) }}...
                                                    </small>
                                                @endif
                                                
                                                <div class="text-muted small mt-1">
                                                    Status: <strong>{{ ucfirst($notification->data['status']) }}</strong>
                                                </div>
                                            </div>
                                            <small class="text-muted text-nowrap ms-2">
                                                {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                            </small>
                                        </a>
                                    @empty
                                        <div class="list-group-item text-center">
                                            <small class="text-muted">Tidak ada notifikasi</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="mx-2 fw-bold">Welcome, {{ Auth::user()->name }}</span>
                                <img src="{{ asset('../assets') }}/img/profile.png" alt="Profile"
                                    style="width: 40px; height: 40px; border-radius: 50%;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item" type="submit">
                                            <i class="bi bi-power"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                    </div>
                </header>

                <div class="page-title">
                    <div class="row mt-3">
                        <div class="col-12 col-md-6 order-md-1 order-last">

                            <h3 class="mb-3 fw-bold">@yield('title')</h3>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@yield('breadcumb')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="page-content">
                    @yield('content')
                </div>
            </div>
        </div>
        <script src="{{ asset('../assets') }}/static/js/components/dark.js"></script>
        <script src="{{ asset('../assets') }}/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).on('click', '.notif-item', function(e) {
                e.preventDefault();

                const notifId = $(this).data('id');
                const targetUrl = $(this).attr('href');
                const token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/notifications/read/' + notifId,
                    method: 'POST',
                    data: {
                        _token: token
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = targetUrl;
                        }
                    },
                    error: function() {
                        window.location.href = targetUrl;
                    }
                });
            });
        </script>
        @if ($errors->any())
            <script>
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }}\n";
                @endforeach

                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessages,
                });
            </script>
        @endif

        @if (session('success') || session('error'))
            <script>
                $(document).ready(function() {
                    var successMessage = "{{ session('success') }}";
                    var errorMessage = "{{ session('error') }}";

                    if (successMessage) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: successMessage,
                        });
                    }

                    if (errorMessage) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                        });
                    }
                });
            </script>
        @endif
        <script src="{{ asset('../assets') }}/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js">
        </script>
        <script src="{{ asset('../assets') }}/compiled/js/app.js"></script>

        <script
            src="{{ asset('../assets') }}/extensions/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js">
        </script>
        <script
            src="{{ asset('../assets') }}/extensions/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js">
        </script>
        <script src="{{ asset('../assets') }}/extensions/simple-datatables/umd/simple-datatables.js"></script>
        <script src="{{ asset('../assets') }}/static/js/pages/simple-datatables.js"></script>
        <script src="{{ asset('../assets') }}/extensions/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js">
        </script>
        <script
            src="{{ asset('../assets') }}/extensions/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js">
        </script>
        <script src="{{ asset('../assets') }}/extensions/filepond-plugin-image-filter/filepond-plugin-image-filter.min.js">
        </script>
        <script src="{{ asset('../assets') }}/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js">
        </script>
        <script src="{{ asset('../assets') }}/extensions/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js">
        </script>
        <script src="{{ asset('../assets') }}/extensions/filepond/filepond.js"></script>
        <script src="{{ asset('../assets') }}/extensions/toastify-js/src/toastify.js"></script>
        <script src="{{ asset('../assets') }}/static/js/pages/dashboard.js"></script>
        <script src="{{ asset('../assets') }}/static/js/pages/filepond.js"></script>

        <script>
            $(document).ready(function() {
                $('.select2').select2();
                $('.rupiah').mask("#.##0", {
                    reverse: true
                });
                $('#datatable').DataTable({
                    responsive: true,
                    paging: true,
                    searching: true,
                    ordering: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
                    }
                });
            });
        </script>

        @yield('scripts')


    </body>

    </html>
