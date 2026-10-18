@extends('layouts.app')

@section('title', 'กระดานงานช่างซ่อมไอที')

@section('content')
<!-- Header with Technician Profile -->
<div class="page-header">
    <div class="page-title">
        <h1>กระดานงานช่างซ่อมไอที</h1>
        <p>
            กำลังดูกระดานของ: <strong>{{ $currentTech->name }}</strong> 
            <span style="color: var(--text-muted);">({{ $currentTech->specialty ?? 'ทีมบริการเทคนิค' }})</span>
        </p>
    </div>

    <!-- Quick Switcher among 4 Technicians -->
    <div style="display: flex; align-items: center; gap: 0.35rem;">
        <span style="font-size: 0.75rem; color: var(--text-muted);">เลือกช่าง:</span>
        @foreach($allTechnicians as $tech)
            <a href="{{ route('technician.dashboard', ['tech_id' => $tech->id]) }}" 
               class="btn btn-sm {{ $currentTech->id == $tech->id ? 'btn-primary' : 'btn-secondary' }}" 
               style="font-size: 0.725rem; padding: 0.25rem 0.5rem;">
                {{ $tech->name }}
            </a>
        @endforeach
    </div>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <p>งานในความรับผิดชอบ</p>
            <h3>{{ $myTickets->count() }}</h3>
        </div>
        <div class="stat-icon">📋</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>กำลังดำเนินการซ่อม</p>
            <h3>{{ $myTickets->whereIn('status', ['in_progress', 'waiting_parts'])->count() }}</h3>
        </div>
        <div class="stat-icon">🔧</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>งานที่เสร็จสิ้นแล้ว</p>
            <h3>{{ $myTickets->where('status', 'completed')->count() }}</h3>
        </div>
        <div class="stat-icon">✅</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>งานว่างที่กดรับได้</p>
            <h3>{{ $unassignedTickets->count() }}</h3>
        </div>
        <div class="stat-icon">⚡</div>
    </div>
</div>

<!-- Section 1: คลังงานรอมอบหมายที่ช่างสามารถ "กดรับงานเองได้" (Claim Tickets) -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main);">
                คลังงานรอมอบหมาย (สามารถกดรับงานได้ทันที)
            </h2>
            <p style="font-size: 0.775rem; color: var(--text-muted);">
                งานที่ยังไม่มีช่างดูแล กดปุ่ม "รับงานนี้" เพื่อดึงเข้าเป็นงานของคุณ
            </p>
        </div>
        <span class="badge badge-pending">
            {{ $unassignedTickets->count() }} งานว่าง
        </span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / ปัญหา</th>
                    <th>สถานที่</th>
                    <th>ผู้แจ้ง / แผนก</th>
                    <th>ความเร่งด่วน</th>
                    <th>เวลาที่แจ้ง</th>
                    <th style="text-align: right;">การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($unassignedTickets as $ticket)
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
                            {{ $ticket->created_at->diffForHumans() }}
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('technician.claim', $ticket->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem;">
                                    กดรับงานนี้
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.825rem;">
                            ไม่มีงานค้างในคลังกลาง ทุกงานมีช่างดูแลครบถ้วนแล้ว
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Section 2: งานในความรับผิดชอบของช่าง (My Tasks) -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main);">
                คิวงานในความรับผิดชอบของ {{ $currentTech->name }}
            </h2>
            <p style="font-size: 0.775rem; color: var(--text-muted);">
                รายการงานที่คุณรับผิดชอบ คลิกเพื่ออัปเดตสถานะและบันทึกผลการซ่อม
            </p>
        </div>
        <span class="badge badge-in_progress">
            {{ $myTickets->whereIn('status', ['in_progress', 'waiting_parts'])->count() }} กำลังทำ
        </span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / หัวข้อปัญหา</th>
                    <th>สถานที่</th>
                    <th>ผู้แจ้ง / เบอร์โทร</th>
                    <th>ความเร่งด่วน</th>
                    <th>สถานะงาน</th>
                    <th>วันที่รับงาน</th>
                    <th style="text-align: right;">จัดการงาน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myTickets as $ticket)
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
                        <td style="font-size: 0.8rem; color: var(--text-muted);">📍 {{ $ticket->location }}</td>
                        <td>
                            <div>{{ $ticket->reporter_name }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">
                                📞 {{ $ticket->phone }}
                            </div>
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
                            @if($ticket->status === 'in_progress')
                                <span class="badge badge-in_progress">กำลังตรวจซ่อม</span>
                            @elseif($ticket->status === 'waiting_parts')
                                <span class="badge badge-waiting_parts">รออะไหล่</span>
                            @elseif($ticket->status === 'completed')
                                <span class="badge badge-completed">เสร็จสิ้นแล้ว</span>
                            @else
                                <span class="badge badge-pending">{{ $ticket->status }}</span>
                            @endif
                        </td>
                        <td style="font-size: 0.775rem; color: var(--text-muted);">
                            {{ $ticket->updated_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                อัปเดตงาน
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.825rem;">
                            ยังไม่มีงานในคิวของคุณ สามารถกดรับงานจากคลังงานว่างด้านบนได้ครับ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
