<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ | IT ServiceDesk</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; background: #f9fafb;">
    <div style="width: 100%; max-width: 820px; display: grid; grid-template-columns: 1fr 1fr; background: #ffffff; border-radius: var(--radius-md); border: 1px solid var(--border); overflow: hidden;">
        
        <!-- Left: Standard Login Form -->
        <div style="padding: 2.25rem 2rem; display: flex; flex-direction: column; justify-content: center;">
            <div style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
                <div class="brand-icon" style="width: 32px; height: 32px; font-size: 0.9rem;">
                    IT
                </div>
                <div>
                    <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main);">IT ServiceDesk</h2>
                    <p style="font-size: 0.725rem; color: var(--text-muted);">ระบบแจ้งซ่อมอุปกรณ์ไอที</p>
                </div>
            </div>

            <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.25rem; color: var(--text-main);">เข้าสู่ระบบ</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                กรอกอีเมลและรหัสผ่านเพื่อเข้าใช้งาน
            </p>

            @if ($errors->any())
                <div class="alert alert-danger" style="padding: 0.6rem 0.85rem; font-size: 0.8rem; margin-bottom: 1rem;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success" style="padding: 0.6rem 0.85rem; font-size: 0.8rem; margin-bottom: 1rem;">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="name@company.local" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; font-size: 0.775rem;">
                    <label style="display: flex; align-items: center; gap: 0.35rem; cursor: pointer; color: var(--text-muted);">
                        <input type="checkbox" name="remember"> จำการเข้าสู่ระบบ
                    </label>
                    <a href="{{ route('tickets.track') }}" style="color: var(--text-muted); text-decoration: none;">
                        ค้นหาสถานะงานซ่อม
                    </a>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>

        <!-- Right: One-Click Demo Switcher (คลิกเพื่อทดสอบ 3 บทบาท) -->
        <div style="background: #f9fafb; border-left: 1px solid var(--border); padding: 2.25rem 2rem; display: flex; flex-direction: column; justify-content: center;">
            <div style="margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.2rem;">
                    สลับบทบาททดสอบด่วน
                </h3>
                <p style="font-size: 0.75rem; color: var(--text-muted);">
                    คลิกเลือกบทบาทเพื่อเข้าสู่ระบบทดสอบได้ทันที:
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <!-- 1. Admin -->
                @if($admin)
                    <a href="{{ route('auth.quickLogin', $admin->id) }}" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.85rem; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main);">
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 600;">👑 แอดมิน (Admin)</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $admin->name }} • แดชบอร์ดและจัดสรรงาน</div>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">&rarr;</span>
                    </a>
                @endif

                <!-- 2. Technicians (4 People) -->
                <div style="background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0.65rem 0.85rem;">
                    <div style="font-size: 0.75rem; font-weight: 600; color: var(--text-main); margin-bottom: 0.4rem;">
                        🛠️ ช่างซ่อมประจำทีม (4 คน)
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.35rem;">
                        @foreach($technicians as $index => $t)
                            <a href="{{ route('auth.quickLogin', $t->id) }}" style="text-decoration: none; padding: 0.35rem 0.5rem; background: var(--bg-subtle); border-radius: var(--radius-sm); font-size: 0.725rem; color: var(--text-main); display: block;">
                                <div style="font-weight: 500;">ช่าง {{ $index + 1 }}: {{ Str::limit($t->name, 12) }}</div>
                                <div style="font-size: 0.65rem; color: var(--text-muted);">{{ $t->specialty }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Normal User -->
                @if($user)
                    <a href="{{ route('auth.quickLogin', $user->id) }}" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.85rem; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text-main);">
                        <div>
                            <div style="font-size: 0.8rem; font-weight: 600;">👤 ผู้ใช้งานทั่วไป (User)</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $user->name }} • ส่งใบแจ้งซ่อม</div>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">&rarr;</span>
                    </a>
                @endif
            </div>

            <div style="margin-top: 1rem; font-size: 0.7rem; color: var(--text-subtle); text-align: center;">
                รหัสผ่านสำหรับทุกบัญชี: <code>password</code>
            </div>
        </div>
    </div>
</body>
</html>
