<nav class="w-full fixed top-0 z-50 border-b shadow-sm" style="background-color:#053A40;">
    <div class="w-full px-3 md:px-5 lg:px-8 py-2
                flex items-center justify-between">

        {{-- LEFT: MENU + LOGO --}}
        <div class="flex items-center gap-2 min-w-0">

            <button id="mobileMenuBtn" type="button" class="text-white flex-shrink-0
                       hover:text-yellow-400
                       focus:outline-none" aria-label="Menu">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <a href="{{ url('/') }}" class="flex items-center gap-2 min-w-0">
                <img src="{{ asset('./images/icon.ico') }}" alt="Icon Computer"
                    class="w-8 h-8 object-contain flex-shrink-0">

                <span class="text-base md:text-lg font-semibold
                           whitespace-nowrap" style="color:#E2B746;">
                    Icon Computer
                </span>
            </a>

        </div>


        {{-- RIGHT --}}
        <div class="flex items-center gap-3 md:gap-6">

            {{-- CART --}}
            <button id="navCartButton" type="button" class="relative text-white
                       hover:text-yellow-400
                       focus:outline-none" aria-label="Shopping cart">
                <i class="fas fa-cart-shopping text-lg"></i>

                <span id="navCartCount" class="absolute flex items-center justify-center
                           min-w-[16px] h-[16px]
                           px-1 rounded-full
                           bg-red-600 text-white
                           text-[9px] font-bold" style="top:-8px; right:-9px;">
                    0
                </span>
            </button>


            {{-- SPECIAL OFFERS --}}
            <a href="{{ url('/?special_offer=1') }}"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs md:text-sm font-bold text-amber-950 shadow-md transition-all duration-300 hover:scale-105 whitespace-nowrap"
                style="background: linear-gradient(135deg, #FFE98B 0%, #F8CE45 50%, #D4AF37 100%); border: 1px solid rgba(255,255,255,0.7);">
                <i class="fas fa-fire text-red-600 animate-bounce"></i>
                <span>Offers</span>
            </a>

            {{-- CONTACT --}}
            <a href="{{ url('/contact') }}" class="text-white hover:text-yellow-400
                       text-sm md:text-base
                       font-medium whitespace-nowrap">
                Contact
            </a>


            {{-- LOGIN / DASHBOARD --}}
            @auth
                @php
                    $userRole = auth()->user()->role ?? 'user';
                    $dashboardRoute = route('userdashboard');
                    if ($userRole === 'admin') {
                        $dashboardRoute = route('dashboard');
                    } elseif ($userRole === 'seller' || $userRole === 'subdealer') {
                        $dashboardRoute = route('sellerdashboard');
                    } elseif ($userRole === 'salesman') {
                        $dashboardRoute = route('salesmandashboard');
                    }
                @endphp
                <a href="{{ $dashboardRoute }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs md:text-sm font-semibold bg-emerald-600 text-white hover:bg-emerald-500 transition-all duration-200 shadow-sm whitespace-nowrap">
                    <i class="fas fa-user-circle text-base"></i>
                    <span>{{ Str::limit(auth()->user()->name ?? 'Dashboard', 12) }}</span>
                </a>
            @else
                <a href="{{ route('login_page') }}"
                    class="inline-flex items-center gap-1.5 text-white hover:text-yellow-400 text-sm md:text-base font-medium whitespace-nowrap">
                    <span>Login</span>
                </a>
            @endauth

        </div>

    </div>
</nav>