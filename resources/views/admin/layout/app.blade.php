<!doctype html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../tamplate/assets/"
  data-template="vertical-menu-template-no-customizer">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Permission - Apps | Vuexy - Bootstrap Admin Template</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../tamplate/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../../tamplate/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../../tamplate/assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../../tamplate/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../../tamplate/assets/vendor/libs/@form-validation/form-validation.css" />

    <!-- Page CSS -->
    <style>
      .swal2-container {
        z-index: 1100 !important; /* Pastikan lebih tinggi dari modal Bootstrap (biasanya 1050) */
      }
    </style>

    <!-- Helpers -->
    <script src="../../tamplate/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../../tamplate/assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @include('admin.layout.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('admin.layout.navbar')
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            @include('admin.layout.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="../../tamplate/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../tamplate/assets/vendor/libs/popper/popper.js"></script>
    <script src="../../tamplate/assets/vendor/js/bootstrap.js"></script>
    <script src="../../tamplate/assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../../tamplate/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../tamplate/assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../../tamplate/assets/vendor/libs/i18n/i18n.js"></script>
    <script src="../../tamplate/assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="../../tamplate/assets/vendor/js/menu.js"></script>

    {{-- ADVANCE --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../../tamplate/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <script src="../../tamplate/assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="../../tamplate/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="../../tamplate/assets/vendor/libs/@form-validation/auto-focus.js"></script>

    <!-- Main JS -->
    <script src="../../tamplate/assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../../tamplate/assets/js/app-access-permission.js"></script>
    <script src="../../tamplate/assets/js/modal-add-permission.js"></script>
    <script src="../../tamplate/assets/js/modal-edit-permission.js"></script>

    @yield('js')
  </body>
</html>
