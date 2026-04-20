<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/backend/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/demo/style2.css') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">


    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    {{-- date picker --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>

    <div class="body-wrapper">

        @include('backend.layouts.asside')

        <div class="main-wrapper mdc-drawer-app-content">

            @include('backend.layouts.header')

            <div class="page-wrapper mdc-toolbar-fixed-adjust">
                <main class="content-wrapper">
                    @yield('content')
                </main>

                @include('backend.layouts.footer')
            </div>

        </div>
    </div>

    <!-- Theme JS (load first) -->
    <script src="{{ asset('assets/backend/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/backend/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/backend/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/backend/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/backend/js/material.js') }}"></script>
    <script src="{{ asset('assets/backend/js/misc.js') }}"></script>
    <script src="{{ asset('assets/backend/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/backend/js/preloader.js') }}"></script>
    <!-- jQuery FIRST (ONLY ONE) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables AFTER jQuery -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!---data table button--->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <!---data table button--->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Toastr CSS + JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- this duplicate cdn used --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- date picker  --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500
            });
        </script>
    @endif


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".has-submenu").forEach(function(trigger) {
                trigger.addEventListener("click", function(e) {
                    e.preventDefault();

                    const wrapper = this.nextElementSibling;
                    const arrow = this.querySelector(".mdc-drawer-arrow");

                    if (wrapper && wrapper.classList.contains("mdc-drawer-submenu-wrapper")) {
                        wrapper.classList.toggle("submenu-open");
                        if (arrow) {
                            arrow.classList.toggle("rotate-arrow");
                        }
                    }
                });
            });
        });


        //modal hide
        function closeModal(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) return;

            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            // Fix Bootstrap Backdrop Bug
            setTimeout(() => {
                document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
                document.body.classList.remove("modal-open");
                document.body.style.overflow = "auto";
            }, 150);
        }

        // GLOBAL SELECT2 INITIALIZER
        function initSelect2() {
            $('.select2').select2({
                width: '100%',
                dropdownParent: $('body'),
                maximumSelectionLength: 5
            });
        }

        $(document).ready(function() {
            initSelect2();
        });

        $(document).on('shown.bs.modal', function(event) {
            $(event.target).find('.select2').select2({
                width: '100%',
                dropdownParent: $(event.target)
            });
        });

        flatpickr(".datepicker", {
            dateFormat: "d-m-Y",
            allowInput: true
        });

        function formatDate(dateString) {
            const d = new Date(dateString);
            return d.toLocaleString("en-IN", {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        }
    </script>

    @stack('footer-script')

</body>

</html>
