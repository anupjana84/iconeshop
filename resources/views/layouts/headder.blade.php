<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 & Custom Alert Enhancer -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom-alerts.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-open .main-content {
            margin-left: 16rem;
        }

        /* for tailwind pagination active link */
        [aria-current="page"],
        [aria-current="page"]>* {
            background-color: #10B981;
            /* green-500 */
            color: #ffffff !important;
            border-radius: 0.375rem;
            /* rounded-md */
            padding: 0.25rem 0.55rem;
            /* match paginator spacing */
            box-shadow: none;
        }
    </style>
    <style>
        #sidebar {
            overflow-y: scroll;
            scrollbar-width: none;
        }

        #sidebar::-webkit-scrollbar {
            display: none;
        }
    </style>


    {{-- <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 h-screen overflow-hidden">
    <div class="flex h-full">
