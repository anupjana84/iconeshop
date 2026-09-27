@include('layouts.headder')
@include('layouts.sidebar')
{{-- <main>
    <div class="main_div">
        <div id="b_a_button">
            @if (Session::has('success'))
            <div class="success">
                <strong><i class='fas fa-bell'></i>Success !</strong>{{ Session::get('success') }}
                <button type="button" class="alart_btn" onclick="hideAlart()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif
            @if (Session::has('fail'))
            <div class="error" role="alert">
                <strong><i class='fas fa-exclamation-triangle'></i>Error !</strong>{{ Session::get('fail') }}
                <button type="button" class="alart_btn" onclick="hideAlart()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            @endif
        </div>
        <div class="main_conent">

        </div>
    </div>
</main> --}}

<!-- Main Content -->
<div id="main-content" class="flex-1 p-3 h-full overflow-y-auto transition-all duration-300">
    {{-- <button onclick="toggleSidebar()" class="md:hidden bg-gray-800 text-white px-4 py-2 rounded">☰</button> --}}
    <div id="alert-container" class="fixed top-0 right-0 p-4 space-y-4 z-50">
        <!-- Success Alert -->
        @if (Session::has('success'))
            <div id="success-alert" class=" bg-green-500 text-white p-4 rounded shadow-md">
                {{ Session::get('success') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof showToast === 'function') {
                        showToast(@json(Session::get('success')), 'success');
                    }
                });
            </script>
        @endif
        <!-- Error Alert -->
        @if (Session::has('fail'))
            <div id="error-alert" class=" bg-red-500 text-white p-4 rounded shadow-md">
                {{ Session::get('fail') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof showToast === 'function') {
                        showToast(@json(Session::get('fail')), 'error');
                    }
                });
            </script>
        @endif
        <!-- Warning Alert -->
        @if (Session::has('warn'))
            <div id="warning-alert" class=" bg-yellow-500 text-white p-4 rounded shadow-md">
                {{ Session::get('warn') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof showToast === 'function') {
                        showToast(@json(Session::get('warn')), 'warning');
                    }
                });
            </script>
        @endif
    </div>
    {{-- <h1 class="text-2xl font-bold">Main Content</h1> --}}
    <div class="flex items-center justify-between pb-2 px-5  bg-gray-100">
        <div class="flex">
            <img src="{{ url('images/icon.ico') }}" alt="Logo" class="w-8 h-8 rounded-full mr-2">
            <!-- Round Logo -->
            <h1 class="text-2xl font-bold text-center">{{ $page_title }}</h1>
        </div>

        <a href="{{ url()->previous() }}"><button type="button"
            class="relative inline-flex items-center justify-center p-0.5 mb-2 me-2 overflow-hidden text-sm font-medium text-gray-900 rounded-lg group bg-gradient-to-br from-teal-300 to-lime-300 group-hover:from-teal-300 group-hover:to-lime-300 dark:text-white dark:hover:text-gray-900 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-lime-800">
            <span
                class="relative px-5 py-2.5 transition-all ease-in duration-75 bg-white dark:bg-gray-900 rounded-md group-hover:bg-transparent group-hover:dark:bg-transparent">
                Go Back
            </span>
        </button></a>
    </div>

    <hr class="border-gray-300 mb-3">
    @yield('content_page')
</div>

@stack('style_link')
@stack('extra_style')
@stack('page_title')
@stack('extra_js')


@include('layouts.footer')

