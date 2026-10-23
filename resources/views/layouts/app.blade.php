<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบแจ้งซ่อมอุปกรณ์ไอที') | IT ServiceDesk</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Clean Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    @php
        $currentUser = Auth::user();
    @endphp

    <!-- Minimalist Top Role Switcher Bar -->
    <div class="demo-role-bar">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-weight: 600; color: var(--text-main);">IT ServiceDesk</span>
            <span style="color: var(--text-subtle);">•</span>
            <span>ผู้ใช้งาน: <strong>{{ $currentUser?->name ?? 'ยังไม่ได้เข้าสู่ระบบ' }}</strong></span>
            <span style="color: var(--text-subtle);">
                ({{ $currentUser?->isAdmin() ? 'แอดมิน' : ($currentUser?->isTechnician() ? 'ช่างไอที' : ($currentUser ? 'พนักงาน' : 'Guest')) }})
            </span>
        </div>

        <div class="demo-role-pills">
            <span style="font-size: 0.725rem; color: var(--text-subtle); margin-right: 0.2rem;">สลับบทบาท:</span>
            <a href="{{ route('auth.quickLogin', 1) }}" class="role-pill {{ $currentUser?->isAdmin() ? 'active' : '' }}">
                แอดมิน
            </a>
            <a href="{{ route('auth.quickLogin', 2) }}" class="role-pill {{ $currentUser?->id == 2 ? 'active' : '' }}">
                ช่าง 1 (สมศักดิ์)
            </a>
            <a href="{{ route('auth.quickLogin', 3) }}" class="role-pill {{ $currentUser?->id == 3 ? 'active' : '' }}">
                ช่าง 2 (อนุชา)
            </a>
            <a href="{{ route('auth.quickLogin', 4) }}" class="role-pill {{ $currentUser?->id == 4 ? 'active' : '' }}">
                ช่าง 3 (ธีรเดช)
            </a>
            <a href="{{ route('auth.quickLogin', 5) }}" class="role-pill {{ $currentUser?->id == 5 ? 'active' : '' }}">
                ช่าง 4 (กิตติศักดิ์)
            </a>
            <a href="{{ route('auth.quickLogin', 6) }}" class="role-pill {{ $currentUser?->id == 6 ? 'active' : '' }}">
                ผู้ใช้ (พรทิพย์)
            </a>
            @if($currentUser)
                <a href="{{ route('logout') }}" class="role-pill" style="margin-left: 0.25rem;">
                    ออกจากระบบ
                </a>
            @else
                <a href="{{ route('login') }}" class="role-pill" style="margin-left: 0.25rem; background: var(--primary); color: #fff;">
                    เข้าสู่ระบบ
                </a>
            @endif
        </div>
    </div>

    <div class="app-wrapper">
        <!-- Minimal Sidebar Navigation -->
        <aside class="app-sidebar">
            <a href="{{ url('/') }}" class="sidebar-brand">
                <div class="brand-icon">IT</div>
                <div class="brand-text">
                    <h2>IT ServiceDesk</h2>
                    <p>ระบบแจ้งซ่อมอุปกรณ์</p>
                </div>
            </a>

            <div class="sidebar-menu">
                @if($currentUser?->isAdmin())
                    <span class="menu-label">สำหรับผู้ดูแลระบบ</span>
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>แดชบอร์ดภาพรวม</span>
                    </a>

                    <a href="{{ route('technician.dashboard') }}" class="nav-link {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                        </svg>
                        <span>มอบหมายงานช่าง (4 คน)</span>
                    </a>

                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>รายการแจ้งซ่อมทั้งหมด</span>
                    </a>
                @elseif($currentUser?->isTechnician())
                    <span class="menu-label">สำหรับช่างซ่อม</span>
                    <a href="{{ route('technician.dashboard') }}" class="nav-link {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                        <span>กระดานงานช่าง / รับงาน</span>
                    </a>

                    <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        <span>ดูงานทั้งหมดในระบบ</span>
                    </a>
                @else
                    <span class="menu-label">สำหรับผู้ใช้งาน</span>
                    <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <span>แจ้งซ่อมอุปกรณ์ใหม่</span>
                    </a>

                @endif

                <span class="menu-label" style="margin-top: 0.75rem;">เมนูทั่วไป</span>
                <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>แจ้งซ่อมด่วน</span>
                </a>

                <a href="{{ route('tickets.track') }}" class="nav-link {{ request()->routeIs('tickets.track') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <span>ค้นหาสถานะงาน</span>
                </a>
            </div>

            <div class="sidebar-footer">
                <div class="user-badge" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="user-avatar">
                            {{ mb_substr($currentUser?->name ?? 'G', 0, 1) }}
                        </div>
                        <div class="user-info">
                            <h4>{{ $currentUser?->name ?? 'ผู้มาเยือน (Guest)' }}</h4>
                            <p>
                                @if($currentUser?->isAdmin()) แอดมิน
                                @elseif($currentUser?->isTechnician()) ช่างไอที
                                @elseif($currentUser) พนักงาน
                                @else ยังไม่ได้เข้าสู่ระบบ
                                @endif
                            </p>
                        </div>
                    </div>
                    @if($currentUser)
                        <a href="{{ route('logout') }}" title="ออกจากระบบ" style="color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; padding: 0.4rem; border-radius: var(--radius-sm); text-decoration: none;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--text-muted)'">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" title="เข้าสู่ระบบ" style="color: var(--primary); display: inline-flex; align-items: center; justify-content: center; padding: 0.4rem; border-radius: var(--radius-sm); text-decoration: none;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="app-main">
            <!-- Top Navbar -->
            <header class="app-header">
                <div class="header-search" style="position: relative;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <form action="{{ route('tickets.index') }}" method="GET">
                        <input type="text" name="search" placeholder="ค้นหารหัสงาน, อุปกรณ์..." value="{{ request('search') }}">
                    </form>
                </div>

                <div class="header-actions">
                    @if($currentUser?->isTechnician())
                        <a href="{{ route('technician.dashboard') }}" class="btn btn-secondary btn-sm">
                            กระดานงานช่าง
                        </a>
                    @elseif($currentUser?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                            แดชบอร์ดแอดมิน
                        </a>
                    @else
                        <a href="{{ route('tickets.track') }}" class="btn btn-secondary btn-sm">
                            ค้นหาสถานะงาน
                        </a>
                    @endif

                    @if($currentUser)
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                            + แจ้งซ่อมใหม่
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                            เข้าสู่ระบบ
                        </a>
                    @endif
                </div>
            </header>

            <!-- Page Body -->
            <div class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
