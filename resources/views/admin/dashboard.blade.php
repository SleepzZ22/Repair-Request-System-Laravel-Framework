@extends('layouts.app')

@section('title', 'แดชบอร์ดผู้ดูแลระบบ')

@section('content')
<!-- Page Title -->
<div class="page-header">
    <div class="page-title">
        <h1>แดชบอร์ดผู้ดูแลระบบ</h1>
        <p>ภาพรวมสถานะงานแจ้งซ่อม สถิติกราฟ และภาระงานของช่างทั้ง 4 คน</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 0.5rem;">
        <a href="{{ route('technician.dashboard') }}" class="btn btn-secondary btn-sm">
            มุมมองช่างซ่อม
        </a>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
            + เปิดใบแจ้งซ่อมใหม่
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <p>งานแจ้งซ่อมทั้งหมด</p>
            <h3>{{ $stats['total'] }}</h3>
        </div>
        <div class="stat-icon">📄</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>งานรอมอบหมายช่าง</p>
            <h3>{{ $stats['unassigned'] }}</h3>
        </div>
        <div class="stat-icon">⏳</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>กำลังดำเนินการซ่อม</p>
            <h3>{{ $stats['in_progress'] }}</h3>
        </div>
        <div class="stat-icon">🔧</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>ซ่อมเสร็จสิ้นแล้ว</p>
            <h3>{{ $stats['completed'] }}</h3>
        </div>
        <div class="stat-icon">✅</div>
    </div>
</div>

<!-- 📊 Charts Section: กราฟวงกลม และ กราฟแท่ง -->
<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 1.25rem; margin-bottom: 1.5rem;">
    <!-- Chart 1: กราฟวงกลม (Donut Chart) สัดส่วนอุปกรณ์ -->
    <div class="card" style="display: flex; flex-direction: column;">
        <div style="margin-bottom: 1rem;">
            <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">
                สัดส่วนงานแจ้งซ่อมตามประเภทอุปกรณ์
            </h2>
            <p style="font-size: 0.75rem; color: var(--text-muted);">
                แยกตามหมวดหมู่อุปกรณ์ที่ส่งเรื่องเข้ามาในระบบ
            </p>
        </div>
        
        <div style="position: relative; height: 260px; display: flex; align-items: center; justify-content: center;">
            <canvas id="categoryDonutChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: กราฟแท่ง (Bar Chart) ภาระงานช่าง 4 คน -->
    <div class="card" style="display: flex; flex-direction: column;">
        <div style="margin-bottom: 1rem;">
            <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">
                เปรียบเทียบภาระงานของช่างประจำทีม (4 คน)
            </h2>
            <p style="font-size: 0.75rem; color: var(--text-muted);">
                จำนวนงานที่กำลังทำอยู่ และงานที่ปิดสำเร็จแล้วของช่างแต่ละท่าน
            </p>
        </div>
        
        <div style="position: relative; height: 260px;">
            <canvas id="technicianBarChart"></canvas>
        </div>
    </div>
</div>

<!-- 4 Technicians Workload Monitor Cards -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">
                รายชื่อช่างประจำทีม (4 คน)
            </h2>
            <p style="font-size: 0.75rem; color: var(--text-muted);">
                ตรวจดูจำนวนงานในมือของช่างแต่ละท่านเพื่อกระจายงานอย่างเหมาะสม
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 0.75rem;">
        @foreach($technicians as $index => $tech)
            <div style="border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0.85rem 1rem; background: #fff; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 600; color: var(--text-main);">{{ $tech->name }}</h4>
                        <span style="font-size: 0.7rem; color: var(--text-subtle);">ช่างคนที่ {{ $index + 1 }}</span>
                    </div>

                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        {{ $tech->specialty ?? 'ช่างไอที' }}
                    </div>

                    <!-- Workload Numbers -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <div style="flex: 1; background: var(--bg-subtle); padding: 0.4rem; border-radius: var(--radius-sm); text-align: center;">
                            <div style="font-size: 0.675rem; color: var(--text-muted);">กำลังทำ</div>
                            <div style="font-size: 1.05rem; font-weight: 700; color: var(--primary);">{{ $tech->active_tasks_count }}</div>
                        </div>
                        <div style="flex: 1; background: var(--bg-subtle); padding: 0.4rem; border-radius: var(--radius-sm); text-align: center;">
                            <div style="font-size: 0.675rem; color: var(--text-muted);">เสร็จแล้ว</div>
                            <div style="font-size: 1.05rem; font-weight: 700; color: #166534;">{{ $tech->completed_tasks_count }}</div>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 0.35rem;">
                    <a href="{{ route('technician.dashboard', ['tech_id' => $tech->id]) }}" class="btn btn-secondary btn-sm" style="flex: 1; font-size: 0.725rem; padding: 0.25rem 0.5rem;">
                        ดูกระดานงาน
                    </a>
                    <a href="{{ route('auth.quickLogin', $tech->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.725rem; padding: 0.25rem 0.5rem;" title="สลับบทบาท">
                        สลับเป็นช่างนี้
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Pending & Unassigned Tickets (แอดมินมอบหมายงานให้ช่างได้ที่นี่) -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">
                รายการงานรอมอบหมายช่าง (Pending Assignment)
            </h2>
            <p style="font-size: 0.75rem; color: var(--text-muted);">
                เลือกช่าง 1 ใน 4 คนเพื่อมอบหมายงาน
            </p>
        </div>
        <span class="badge badge-pending">
            {{ $tickets->whereNull('technician_id')->where('status', 'pending')->count() }} งานรอมอบหมาย
        </span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / หัวข้อปัญหา</th>
                    <th>สถานที่</th>
                    <th>ผู้แจ้ง</th>
                    <th>ความเร่งด่วน</th>
                    <th>วันที่แจ้ง</th>
                    <th style="min-width: 240px;">มอบหมายให้ช่าง</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets->whereNull('technician_id')->where('status', 'pending') as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-id">
                                {{ $ticket->ticket_no }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600;">{{ $ticket->title }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">{{ $ticket->category }}</div>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">📍 {{ $ticket->location }}</td>
                        <td>
                            <div>{{ $ticket->reporter_name }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">{{ $ticket->department }}</div>
                        </td>
                        <td>
                            @if($ticket->priority === 'urgent')
                                <span class="priority-pill priority-urgent">ด่วนที่สุด</span>
                            @elseif($ticket->priority === 'high')
                                <span class="priority-pill priority-high">ด่วน</span>
                            @else
                                <span class="priority-pill priority-normal">ปกติ</span>
                            @endif
                        </td>
                        <td style="font-size: 0.775rem; color: var(--text-muted);">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <form action="{{ route('admin.assign', $ticket->id) }}" method="POST" style="display: flex; gap: 0.35rem;">
                                @csrf
                                <select name="technician_id" class="form-select" style="padding: 0.35rem 0.5rem; font-size: 0.775rem;" required>
                                    <option value="">-- เลือกช่าง --</option>
                                    @foreach($technicians as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->name }} (คิว: {{ $t->active_tasks_count }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem;">
                                    มอบหมาย
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.825rem;">
                            ไม่มีงานรอมอบหมายในขณะนี้ ทุกงานได้รับการจัดสรรช่างเรียบร้อยแล้ว
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- All Tickets Table Summary -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 0.95rem; font-weight: 700; color: var(--text-main);">
                รายการแจ้งซ่อมล่าสุด
            </h2>
            <p style="font-size: 0.75rem; color: var(--text-muted);">ประวัติการแจ้งซ่อมและสถานะปัจจุบันของอุปกรณ์</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
            ดูทั้งหมด &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / หัวข้อ</th>
                    <th>ผู้แจ้ง / แผนก</th>
                    <th>ช่างผู้รับผิดชอบ</th>
                    <th>ความเร่งด่วน</th>
                    <th>สถานะ</th>
                    <th style="text-align: right;">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets->take(6) as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-id">
                                {{ $ticket->ticket_no }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $ticket->title }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">{{ $ticket->category }}</div>
                        </td>
                        <td>
                            <div>{{ $ticket->reporter_name }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">{{ $ticket->department }}</div>
                        </td>
                        <td>
                            @if($ticket->technician)
                                <span style="font-size: 0.8rem; color: var(--text-main);">
                                    {{ $ticket->technician->name }}
                                </span>
                            @else
                                <span style="font-size: 0.725rem; color: var(--text-subtle);">
                                    - ยังไม่ระบุ -
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->priority === 'urgent')
                                <span class="priority-pill priority-urgent">ด่วนที่สุด</span>
                            @elseif($ticket->priority === 'high')
                                <span class="priority-pill priority-high">ด่วน</span>
                            @else
                                <span class="priority-pill priority-normal">ปกติ</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket->status === 'pending')
                                <span class="badge badge-pending">รอดำเนินการ</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge badge-in_progress">กำลังซ่อม</span>
                            @elseif($ticket->status === 'waiting_parts')
                                <span class="badge badge-waiting_parts">รออะไหล่</span>
                            @elseif($ticket->status === 'completed')
                                <span class="badge badge-completed">เสร็จสิ้น</span>
                            @else
                                <span class="badge badge-cancelled">ยกเลิก</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                รายละเอียด
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. กราฟวงกลมแบบโดนัท (Donut Chart) - หมวดหมู่อุปกรณ์
    const categoryCtx = document.getElementById('categoryDonutChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categoryChart['labels']),
                datasets: [{
                    data: @json($categoryChart['data']),
                    backgroundColor: [
                        '#2563eb', // Blue (Computer)
                        '#0ea5e9', // Sky (Printer)
                        '#8b5cf6', // Purple (Network)
                        '#10b981', // Green (Software)
                        '#94a3b8'  // Gray (Other)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: { family: "'Prompt', sans-serif", size: 12 },
                            boxWidth: 12,
                            padding: 12,
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Prompt', sans-serif", size: 13 },
                        bodyFont: { family: "'Prompt', sans-serif", size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }

    // 2. กราฟแท่ง (Bar Chart) - เปรียบเทียบงานของช่างทั้ง 4 คน
    const techCtx = document.getElementById('technicianBarChart');
    if (techCtx) {
        new Chart(techCtx, {
            type: 'bar',
            data: {
                labels: @json($technicianChart['labels']),
                datasets: [
                    {
                        label: 'กำลังซ่อม (Active)',
                        data: @json($technicianChart['active']),
                        backgroundColor: '#2563eb',
                        borderRadius: 4,
                        maxBarThickness: 24,
                    },
                    {
                        label: 'ซ่อมเสร็จสิ้น (Done)',
                        data: @json($technicianChart['completed']),
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        maxBarThickness: 24,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Prompt', sans-serif", size: 12 }, color: '#475569' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 11 } },
                        grid: { color: '#f1f5f9' }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            font: { family: "'Prompt', sans-serif", size: 12 },
                            boxWidth: 10,
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: "'Prompt', sans-serif", size: 13 },
                        bodyFont: { family: "'Prompt', sans-serif", size: 12 },
                        padding: 10,
                        cornerRadius: 8
                    }
                }
            }
        });
    }
});
</script>
@endsection
