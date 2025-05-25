<!-- Навигационная панель Bootstrap -->
<nav class="navbar navbar-expand-md navbar-light bg-transparent navbar-custom fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center hover-lift" href="{{ url('/') }}">
            <div class="brand-icon-wrapper me-2 rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center">
                <i class="fa-solid fa-certificate text-primary"></i>
            </div>
            <span class="fw-bold">{{ config('app.name', 'Laravel') }}</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link nav-hover px-3" href="#benefits">
                        <i class="ri-award-line me-1"></i> Преимущества
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover px-3" href="#how-it-works">
                        <i class="ri-flow-chart me-1"></i> Как это работает
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover px-3" href="#pricing">
                        <i class="ri-money-dollar-circle-line me-1"></i> Тарифы
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-hover px-3" href="#faq">
                        <i class="ri-question-answer-line me-1"></i> FAQ
                    </a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="{{ route('login') }}">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> {{ __('Login') }}
                            </a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary rounded-pill px-4 ms-2 hover-lift" href="{{ route('register') }}">
                                <i class="fa-solid fa-user-plus me-1"></i> {{ __('Register') }}
                            </a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center nav-hover" href="{{ url('/home') }}">
                            <i class="fa-solid fa-gauge-high me-1"></i> Панель
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <div class="avatar-circle me-2 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center">
                                <i class="fa-solid fa-user text-primary"></i>
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end rounded-4 border-0 shadow-sm" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fa-regular fa-user me-2 text-primary"></i> Мой профиль
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket me-2 text-danger"></i> {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
