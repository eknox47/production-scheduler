<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Production Scheduler')</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-light">
        <header>
            <nav class="navbar navbar-dark bg-dark">
                <div class="container">
                    <a class="navbar-brand fw-bold" href="{{ route('schedule') }}">
                        Production Scheduler
                    </a>

                    <div class="d-flex gap-3">
                        <a class="nav-link text-white" href="{{ route('orders.create') }}">
                            Create Order
                        </a>

                        <a class="nav-link text-white" href="{{ route('orders.index') }}">
                            View Orders
                        </a>

                        <a class="nav-link text-white" href="{{ route('schedule') }}">
                            Production Schedule
                        </a>
                    </div>
                </div>
            </nav>
        </header>

        <main class="container py-3">
            @yield('content')
        </main>

        @stack('scripts')

    </body>
</html>
