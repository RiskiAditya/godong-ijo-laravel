@extends('admin.layouts.app')

@section('content')
<div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-gradient-to-b from-green-800 to-green-900 text-white w-64 min-h-screen flex-shrink-0 fixed md:static z-20 transition-transform duration-300"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        
        <!-- Logo -->
        <div class="p-6 border-b border-green-700">
            <h1 class="text-2xl font-bold">Godong Ijo</h1>
            <p class="text-sm text-green-300 mt-1">Admin Panel</p>
        </div>
        
        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700/50' }}">
                <i class="fas fa-chart-line w-5"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.bookings.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.bookings.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700/50' }}">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Booking</span>
            </a>
            
            <a href="{{ route('admin.paket-wisata.index') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('admin.paket-wisata.*') ? 'bg-green-700 text-white' : 'text-green-100 hover:bg-green-700/50' }}">
                <i class="fas fa-box w-5"></i>
                <span>Paket Wisata</span>
            </a>
            
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-8">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-green-100 hover:bg-red-700/50 w-full">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen">
        <!-- Top Bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 py-4 px-6 flex items-center justify-between">
            <!-- Mobile Menu Button -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="md:hidden text-gray-600 hover:text-gray-900">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
            
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    <i class="fas fa-user-circle mr-2"></i>
                    {{ auth()->guard('admin')->user()->name }}
                </span>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="flex-1 p-6">
            <!-- Flash Messages -->
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                 class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
                <button @click="show = false" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            @yield('page-content')
        </main>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden">
    </div>
</div>
@endsection
