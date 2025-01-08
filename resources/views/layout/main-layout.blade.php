<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGT Solutions</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5.3 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/ol@v10.0.0/dist/ol.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v10.0.0/ol.css">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">


    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            padding-top: 70px;
            /* Adjust this value depending on the height of your navbar */
        }

        #map {
            height: 89vh;
            width: 100%;
        }

        /* Custom styles for offcanvas links */
        .offcanvas-body .nav-link {
            color: #343a40;
            /* Default link color */
            padding: 15px;
            /* Padding for links */
            font-weight: 500;
            /* Bold text */
            transition: background-color 0.3s;
            /* Smooth background transition */
        }

        .offcanvas-body .nav-link:hover {
            background-color: #f8f9fa;
            /* Light background on hover */
            color: #007bff;
            /* Change text color on hover */
        }

        .offcanvas-body .nav-link.active {
            background-color: #e9ecef;
            /* Active link background */
            color: #495057;
            /* Active link text color */
        }

        .offcanvas-body .dropdown-menu {
            background-color: #f8f9fa;
            /* Dropdown menu background */
        }

        .offcanvas-body .dropdown-item {
            color: #343a40;
            /* Dropdown item color */
        }

        .offcanvas-body .dropdown-item:hover {
            background-color: #007bff;
            /* Dropdown item hover background */
            color: white;
            /* Dropdown item hover text color */
        }

        .model {
            position: relative;
            /* Ensure modal itself is positioned */
        }

        #flash-message {
            position: absolute;
            /* Position it relative to the modal */
            top: 10px;
            /* Adjust the position from the top of the modal */
            left: 50%;
            transform: translateX(-50%);
            /* Center the flash message horizontally */
            z-index: 1060;
            /* Ensure it is above modal content */
            min-width: 200px;
            max-width: 350px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-weight: bold;
            display: block;
        }

        /* Flash message style adjustments */
        #flash-message-success,
        #flash-message-error {
            position: absolute;
            top: 10px;
            /* Adjust based on how you want the flash message to appear */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1060;
            display: none;
            /* Set to block or inline-block when you want to show it */
            min-width: 200px;
            max-width: 350px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="flash-message-success" class="alert alert-success alert-dismissible fade show" role="alert"
        style="display: none;">
        <span id="flash-message-success-content"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div id="flash-message-error" class="alert alert-danger alert-dismissible fade show" role="alert"
        style="display: none;">
        <span id="flash-message-error-content"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>


    <nav class="navbar bg-body-tertiary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('img/logo.png') }}" alt="" style="width: 150px; height: auto;">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Offcanvas Navbar -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                @if (Auth::guard('admin')->check())
                    <p>Welcome, Admin!</p>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.surveyors') }}">
                            <i class="fas fa-compass"></i> Surveyors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.cbe') }}">
                            <i class="fas fa-building"></i> CBE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-money-bill-wave"></i> Tax Collectors
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i> Dropdown
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user"></i> Action</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-clipboard"></i> Another
                                    action</a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-ellipsis-h"></i> Something else
                                    here</a></li>
                        </ul>
                    </li>
                    <!-- Admin-specific content -->
                @elseif (Auth::guard('surveyor')->check())
                    <p>Welcome, {{ Auth::guard('surveyor')->user()->name }}!</p>
                    <h3>Total Count : <span id="surveycount">{{ $pointCount ?? null }}<spam>
                    </h3>
                    <form class="d-flex mt-3" action="{{ route('surveyor.findGisid') }}">
                        <input class="form-control me-2" type="text" id="sgisid" name="sgisid">
                        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i>
                            Search</button>
                    </form>
                @else
                    <p>Welcome, User!</p>
                @endif


                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>

        </div>
    </div>

    @yield('content')
</body>
<Script>
    document.addEventListener("contextmenu", function(e) {
        e.preventDefault();
    });

    // Disable common inspect shortcuts
    document.addEventListener("keydown", function(e) {
        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        if (
            e.key === "F12" ||
            (e.ctrlKey && e.shiftKey && (e.key === "I" || e.key === "J")) ||
            (e.ctrlKey && e.key === "U")
        ) {
            e.preventDefault();
        }
    });
</Script>
<script src="{{ asset('js/main.js') }}"></script>

</html>
