<!DOCTYPE html>
<html>
<head>
    <title>School Management System</title>
    <!-- Add CSS/JS here -->
    <style> /* Basic styling for demonstration */
        body { display: flex; font-family: sans-serif; }
        .sidebar { width: 250px; background: #f4f4f4; padding: 15px; height: 100vh; }
        .main-content { flex-grow: 1; padding: 20px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li a { display: block; padding: 8px; text-decoration: none; color: #333; }
        .sidebar ul ul { padding-left: 20px; }
        .user-info { margin-bottom: 20px; }
        .user-info img { width: 50px; height: 50px; border-radius: 50%; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="user-info">
            @auth
                <img src="{{ Auth::user()->profile_picture ? asset('public/storage/' . Auth::user()->profile_picture) : asset('public/storage/images/default_avatar.png') }}" alt="Profile Picture">
                <p>Welcome, {{ Auth::user()->name }}</p>
                <p>({{ Auth::user()->role->name }})</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @endauth
        </div>

        <nav>
            <ul>
                <!-- Dynamic Navigation -->
                @isset($navItems)
                    @foreach ($navItems as $navItem)
                        <li>
                            {{-- If the nav item has a route, use it. Otherwise, use '#' to make it a dead link (for dropdowns). --}}
                            <a href="{{ $navItem->route ? route($navItem->route) : '#' }}">
                                @if($navItem->icon) <i class="fas {{ $navItem->icon }}"></i> @endif {{-- Example for displaying icons --}}
                                {{ $navItem->name }}
                            </a>
                            @if ($navItem->children->count() > 0)
                                <ul class="submenu"> {{-- Added a class for potential styling --}}
                                    @foreach ($navItem->children as $child)
                                        <li>
                                            <a href="{{ route($child->route) }}">
                                                @if($child->icon) <i class="fas {{ $child->icon }}"></i> @endif
                                                {{ $child->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @endisset
            </ul>
        </nav>
    </div>

    <div class="main-content">
        @yield('content')
    </div>

    @stack('scripts') {{-- <-- ADD THIS LINE HERE --}}  
</body>
</html>