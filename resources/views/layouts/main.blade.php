<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="{{asset('js/main.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <!-- jquery Cdn -->
    <script src="https://code.jquery.com/jquery-3.6.3.js"
        integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>

    <!-- bootstrp Cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <!-- font Awesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- sweet Aler cdn -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</head>

<body>
    <nav class="navbar navbar-expand-lg bg-dark">
        <div class="container-fluid">
            @guest
            <a class="navbar-brand text-white" href="{{route('home')}}">Logo</a>
            @endguest
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @auth
                    <li class="nav-item mr-3">
                        <a class="nav-link text-white" href="{{route('dashboard')}}">Summary Reports</a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link text-white" href="{{route('companyWiseSalesReport')}}">Company Sales
                            Report</a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link text-white" href="{{route('salesReport')}}">Sales/Revenue Report</a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link text-white" href="{{route('commReport')}}">Commission Report</a>
                    </li>

                    @endauth
                </ul>
                <div>
                    @auth
                    <span class="text-white mr-3">Hello, {{Auth::user()->name}}</span>
                    <a class="text-white" href="{{route('logout')}}">Log Out</a>
                    @else
                    <a class="text-white" href="{{route('login')}}">Login</a>
                    <a class="text-white" href="{{route('register')}}">Register</a>
                    @endauth
                </div>

            </div>
        </div>

    </nav>
    <div class="container-fluid">
        @yield('content')
    </div>
</body>

</html>