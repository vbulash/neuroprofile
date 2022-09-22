@extends('layouts.skeleton')

@section('body')
  <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed main-content-narrow page-header-dark sidebar-dark side-trans-enabled">
    <nav id="sidebar" aria-label="Main Navigation">
      <!-- Side Header -->
      <div class="bg-header-dark">
        <div class="content-header bg-white-5">
          <!-- Logo -->
          <a class="fw-semibold text-white tracking-wide" href="/">
              {!! env('APP_NAME') !!}
          </a>
          <!-- END Logo -->

          <!-- Options -->
          <div>

            <!-- Close Sidebar, Visible only on mobile screens -->
            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
            <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none" data-toggle="layout" data-action="sidebar_close">
              <i class="fa fa-times-circle"></i>
            </button>
            <!-- END Close Sidebar -->
          </div>
          <!-- END Options -->
        </div>
      </div>
      <!-- END Side Header -->

      <!-- Sidebar Scrolling -->
      <div class="js-sidebar-scroll">
        <!-- Side Navigation -->
		  @include('layouts.partials.navigation')
        <!-- END Side Navigation -->
      </div>
      <!-- END Sidebar Scrolling -->
    </nav>
    <!-- END Sidebar -->

    <!-- Header -->
    <header id="page-header">
      <!-- Header Content -->
      <div class="content-header">
        <!-- Left Section -->
        <div class="space-x-1">
          <!-- Toggle Sidebar -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
          <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
          </button>
          <!-- END Toggle Sidebar -->

          <!-- Open Search Section -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
{{--          <button type="button" class="btn btn-alt-secondary" data-toggle="layout" data-action="header_search_on" disabled>--}}
{{--            <i class="fa fa-fw opacity-50 fa-search"></i> <span class="ms-1 d-none d-sm-inline-block">Поиск</span>--}}
{{--          </button>--}}
          <!-- END Open Search Section -->
        </div>
        <!-- END Left Section -->

        <!-- Right Section -->
        <div class="space-x-1">
            @include('layouts.partials.userpanel')
        </div>
        <!-- END Right Section -->
      </div>
      <!-- END Header Content -->

      <!-- Header Search -->
{{--      <div id="page-header-search" class="overlay-header bg-header-dark">--}}
{{--        <div class="content-header">--}}
{{--          <form class="w-100" action="/" method="POST">--}}
{{--            @csrf--}}
{{--            <div class="input-group">--}}
{{--              <!-- Layout API, functionality initialized in Template._uiApiLayout() -->--}}
{{--              <button type="button" class="btn btn-alt-primary" data-toggle="layout" data-action="header_search_off">--}}
{{--                <i class="fa fa-fw fa-times-circle"></i>--}}
{{--              </button>--}}
{{--              <input type="text" class="form-control border-0" placeholder="Поиск или нажмите клавишу ESC.." id="page-header-search-input" name="page-header-search-input">--}}
{{--            </div>--}}
{{--          </form>--}}
{{--        </div>--}}
{{--      </div>--}}
      <!-- END Header Search -->

      <!-- Header Loader -->
      <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
      <div id="page-header-loader" class="overlay-header bg-header-dark">
        <div class="bg-white-10">
          <div class="content-header">
            <div class="w-100 text-center">
              <i class="fa fa-fw fa-sun fa-spin text-white"></i>
            </div>
          </div>
        </div>
      </div>
      <!-- END Header Loader -->
    </header>
    <!-- END Header -->

    <!-- Main Container -->
    <main id="main-container">
      @yield('content')
    </main>
    <!-- END Main Container -->

      @include('layouts.partials.footer')
  </div>
  <!-- END Page Container -->
@endsection
