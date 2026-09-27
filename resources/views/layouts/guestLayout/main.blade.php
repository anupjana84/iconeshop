@include('layouts.guestLayout.headder')

@include('layouts.guestLayout.navbar')

{{-- SIDEBAR OVERLAY --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/60 z-[99998] hidden transition-opacity duration-300 opacity-0"></div>

{{-- SIDEBAR MENU (Sliding Drawer) --}}
<div id="sidebarMenu" class="fixed top-0 left-0 h-screen w-80 bg-white z-[99999] transform -translate-x-full transition-transform duration-300 ease-in-out shadow-2xl flex flex-col invisible">
    {{-- Header --}}
    <div class="p-5 border-b flex items-center justify-between bg-green-600 text-white">
        <h2 class="text-xl font-bold">Categories</h2>
        {{-- Close Button with explicit padding and text size --}}
        <button id="sidebarClose" class="text-white hover:text-green-50 transition-colors flex items-center justify-center w-10 h-10 rounded-full hover:bg-white/10" aria-label="Close menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Category List --}}
    <div class="flex-1 overflow-y-auto py-4 px-3">
        @php
            $sidebarCategories = \App\Models\Category::orderBy('name', 'asc')->get();
        @endphp
        <ul class="space-y-1.5">
            @foreach ($sidebarCategories as $category)
                <li>
                    <a href="{{ url('/?category=' . $category->id) }}" class="flex items-center gap-4 px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-xl transition-all border border-transparent hover:border-green-100 group">
                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:border-green-200 transition-colors">
                            @if ($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-lg">📦</span>
                            @endif
                        </div>
                        <span class="font-medium text-base">{{ $category->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Footer --}}
    <div class="p-5 border-t bg-gray-50">
        <p class="text-xs text-center text-gray-500 font-medium">© {{ date('Y') }} Icon Computer</p>
        <p class="text-xs text-center text-gray-500 font-medium">
        <a href="https://www.iconcomputer.in/terms.php" target="_blank" class="hover:text-green-600 transition">
            Terms of Use
        </a>
        <span class="mx-2">|</span>
        <a href="https://www.iconcomputer.in/privacy.php" target="_blank" class="hover:text-green-600 transition">
            Privacy Policy
        </a>
    </p>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const mobileMenuBtn = document.getElementById("mobileMenuBtn");
        const sidebarMenu = document.getElementById("sidebarMenu");
        const sidebarOverlay = document.getElementById("sidebarOverlay");
        const sidebarClose = document.getElementById("sidebarClose");

        function openSidebar() {
            sidebarMenu.classList.remove("invisible");
            sidebarOverlay.classList.remove("hidden");
            // Allow layout to settle before animation
            setTimeout(() => {
                sidebarOverlay.classList.remove("opacity-0");
                sidebarMenu.classList.remove("-translate-x-full");
            }, 10);
            document.body.style.overflow = "hidden";
        }

        function closeSidebar() {
            sidebarOverlay.classList.add("opacity-0");
            sidebarMenu.classList.add("-translate-x-full");
            document.body.style.overflow = "";
            // Delay hide until after transition
            setTimeout(() => {
                sidebarOverlay.classList.add("hidden");
                sidebarMenu.classList.add("invisible");
            }, 300);
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener("click", openSidebar);
        if (sidebarClose) sidebarClose.addEventListener("click", closeSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener("click", closeSidebar);
    });
</script>

@yield('content_page')

@stack('style_link')
@stack('extra_style')
@stack('page_title')
@stack('extra_js')


@include('layouts.guestLayout.footer')
