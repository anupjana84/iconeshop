<nav class="w-full bg-white shadow-sm border-b px-4 md:px-10 py-3 flex items-center justify-between fixed top-0 z-50 " style="background-color:#053A40;">

    {{-- LEFT: Hamburger & Logo --}}
    <div class="flex items-center space-x-4">
        {{-- Hamburger Icon --}}
        <button id="mobileMenuBtn" class="mr-2 text-white hover:text-green-600 focus:outline-none transition-colors">
            <i class="fas fa-bars text-2xl"></i>
        </button>

        <a href="/" class="flex items-center space-x-3">
            <img src="{{ asset('./images/icon.ico') }}" class="h-9" alt="Logo">
            <span class="text-xl md:text-2xl font-semibold text-yellow-500" style="color: #E2B746">Icon Computer</span>
        </a>
    </div>
    {{-- RIGHT: Links --}}
    <div class="flex items-center gap-4">
        <button id="navCartButton" type="button"
            class="relative text-white hover:text-yellow-400 focus:outline-none transition-colors"
            aria-label="Open shopping cart" title="Shopping cart">
            <i class="fas fa-cart-shopping text-xl"></i>
            <span id="navCartCount"
                class="absolute min-w-5 h-5 px-1 rounded-full bg-red-600 text-white text-xs flex items-center justify-center"
                style="top: -10px; right: -10px;">
                0
            </span>
        </button>

        @guest
            <a href="{{ route('login_page') }}"
                class="inline-flex items-center gap-2 text-white hover:text-yellow-400 transition-colors"
                aria-label="Login" title="Login">
                <i class="fas fa-right-to-bracket text-xl"></i>
                <span class="hidden sm:inline">Login</span>
            </a>
        @endguest

        {{-- Desktop Navigation --}}
        <ul class="hidden md:flex items-center gap-6 text-lg font-semibold">
            <li>
                <a href="/" class="{{ request()->is('/') ? 'text-yellow-400 font-bold' : 'text-white hover:text-yellow-400' }} transition-colors">
                    Home
                </a>
            </li>
            <li>
                <a href="/contact" class="{{ request()->is('contact') ? 'text-yellow-400 font-bold' : 'text-white hover:text-yellow-400' }} transition-colors">
                    Contact
                </a>
            </li>
            <li>
                <a href="{{ auth()->check() ? route('userdashboard') : route('login_page') }}" class="{{ request()->is('support') ? 'text-yellow-400 font-bold' : 'text-white hover:text-yellow-400' }} transition-colors">
                    Support
                </a>
            </li>
        </ul>

        {{-- Mobile Navigation (Icons) --}}
        <div class="flex md:hidden items-center space-x-5 mr-3">
            <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'text-yellow-400' : 'text-white' }} hover:text-yellow-400 transition-colors cursor-pointer p-1">
                <i class="fas fa-home text-2xl"></i>
            </a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact') ? 'text-yellow-400' : 'text-white' }} hover:text-yellow-400 transition-colors cursor-pointer p-1">
                <i class="fas fa-phone text-2xl"></i>
            </a>
            <a href="{{ auth()->check() ? route('userdashboard') : route('login_page') }}"
                class="text-white hover:text-yellow-400 transition-colors cursor-pointer p-1"
                aria-label="Support" title="Support">
                <i class="fas fa-headset text-2xl"></i>
            </a>
        </div>
    </div>

</nav>




