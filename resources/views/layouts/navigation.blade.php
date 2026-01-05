<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <x-application-logo style="height: 40px; width: auto;"></x-application-logo>
            <span class="ms-2 fw-bold text-dark">Toko Surya Kencana</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') || request()->is('/') ? 'active fw-bold' : '' }}"
                        href="{{ route('home') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active fw-bold' : '' }}"
                        href="{{ route('products.index') }}">
                        Product
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cart.*') ? 'active fw-bold' : '' }}"
                        href="{{ route('cart.index') }}">
                        Cart
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transaction.*') ? 'active fw-bold' : '' }}"
                        href="{{ auth()->user()?->role === 'admin' ? route('transaction.admin') : route('transaction.index') }}">
                        Transaction
                    </a>
                </li>

                <li class="nav-item">
                    @php
                        $chatRoute = route('chat.support');
                        if (auth()->check() && auth()->user()->role == 'admin') {
                            $chatRoute = route('chat.admin');
                        }
                    @endphp

                    <a class="nav-link {{ request()->routeIs('chat.*') ? 'active fw-bold' : '' }}"
                        href="{{ $chatRoute }}">
                        Chat
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('addresses.*') ? 'active fw-bold' : '' }}"
                        href="{{ route('addresses.index') }}">
                        Address
                    </a>
                </li>

                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()?->username }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
