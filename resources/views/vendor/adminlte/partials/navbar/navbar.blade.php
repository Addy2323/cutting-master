@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

<nav class="main-header navbar
    {{ config('adminlte.classes_topnav_nav', 'navbar-expand') }}
    {{ config('adminlte.classes_topnav', 'navbar-white navbar-light') }}">

    {{-- Navbar left links --}}
    <ul class="navbar-nav">
        {{-- Left sidebar toggler link --}}
        @include('adminlte::partials.navbar.menu-item-left-sidebar-toggler')

        {{-- Configured left links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item')

        {{-- Custom left links --}}
        @yield('content_top_nav_left')
    </ul>

    {{-- Navbar right links --}}
    <ul class="navbar-nav ml-auto">
        {{-- Notifications --}}
        @auth
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    @php
                        $unreadCount = \App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge badge-warning navbar-badge">{{ $unreadCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">{{ $unreadCount }} New Notifications</span>
                    <div class="dropdown-divider"></div>
                    @foreach(\App\Models\Notification::where('user_id', auth()->id())
                        ->where('is_read', false)
                        ->latest()
                        ->take(5)
                        ->get() as $notification)
                        <a href="{{ $notification->link }}" class="dropdown-item">
                            <i class="fas fa-calendar mr-2"></i> {{ $notification->message }}
                            <span class="float-right text-muted text-sm">{{ $notification->created_at->diffForHumans() }}</span>
                        </a>
                        <div class="dropdown-divider"></div>
                    @endforeach
                    <a href="{{ route('notifications.index') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
        @endauth

        {{-- Custom right links --}}
        @yield('content_top_nav_right')

        {{-- Configured right links --}}
        @each('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item')

        {{-- User menu link --}}
        @if(Auth::user())
            @if(config('adminlte.usermenu_enabled'))
                @include('adminlte::partials.navbar.menu-item-dropdown-user-menu')
            @else
                @include('adminlte::partials.navbar.menu-item-logout-link')
            @endif
        @endif

        {{-- Right sidebar toggler link --}}
        @if($layoutHelper->isRightSidebarEnabled())
            @include('adminlte::partials.navbar.menu-item-right-sidebar-toggler')
        @endif
    </ul>

</nav>
