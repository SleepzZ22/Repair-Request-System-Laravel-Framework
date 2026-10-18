@extends('layouts.app')

@section('title', 'ประวัติงานแจ้งซ่อมของฉัน')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-title">
        <h1>ประวัติงานแจ้งซ่อมของฉัน</h1>
        <p>ติดตามความคืบหน้าและผลการซ่อมของอุปกรณ์ที่คุณได้แจ้งไว้</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
            + แจ้งซ่อมใหม่
        </a>
    </div>
</div>

<!-- Stats for User -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <p>รายการที่แจ้งทั้งหมด</p>
            <h3>{{ $tickets->count() }}</h3>
        </div>
        <div class="stat-icon">📄</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>รอดำเนินการ</p>
            <h3>{{ $tickets->where('status', 'pending')->count() }}</h3>
        </div>
        <div class="stat-icon">⏳</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>กำลังตรวจซ่อม</p>
            <h3>{{ $tickets->whereIn('status', ['in_progress', 'waiting_parts'])->count() }}</h3>
        </div>
        <div class="stat-icon">🔧</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <p>ซ่อมเสร็จสิ้นแล้ว</p>
            <h3>{{ $tickets->where('status', 'completed')->count() }}</h3>
        </div>
        <div class="stat-icon">✅</div>
    </div>
</div>

<!-- My Tickets Table -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
            <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main);">
                รายการอุปกรณ์ที่แจ้งซ่อม
            </h2>
            <p style="font-size: 0.775rem; color: var(--text-muted);">
                คลิกดูรายละเอียดเพื่อติดตามขั้นตอนการทำงาน
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / ปัญหา</th>
                    <th>สถานที่</th>
                    <th>ช่างผู้รับผิดชอบ</th>
                    <th>ความเร่งด่วน</th>
                    <th>สถานะ</th>
                    <th>วันที่แจ้ง</th>
                    <th style="text-align: right;">ติดตามผล</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-id">
                                {{ $ticket->ticket_no }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 500; color: var(--text-main);">{{ $ticket->title }}</div>
                            <div style="font-size: 0.725rem; color: var(--text-muted);">
                                {{ $ticket->category }}
                                @if(!empty($ticket->asset_code))
                                    • <code>{{ $ticket->asset_code }}</code>
                                @endif
                            </div>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted);">📍 {{ $ticket->location }}</td>
                        <td>
                            @if($ticket->technician)
                                <div style="font-weight: 500; font-size: 0.8rem;">
                                    {{ $ticket->technician->name }}
                                </div>
                                <div style="font-size: 0.7rem; color: var(--text-subtle);">
                                    {{ $ticket->technician->specialty ?? 'ช่างไอที' }}
                                </div>
                            @else
                                <span style="font-size: 0.725rem; color: var(--text-subtle);">
                                    รอจัดสรรช่าง
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
                                <span class="badge badge-pending">รับเรื่องแล้ว</span>
                            @elseif($ticket->status === 'in_progress')
                                <span class="badge badge-in_progress">กำลังซ่อม</span>
                            @elseif($ticket->status === 'waiting_parts')
                                <span class="badge badge-waiting_parts">รออะไหล่</span>
                            @elseif($ticket->status === 'completed')
                                <span class="badge badge-completed">เสร็จสิ้นแล้ว</span>
                            @else
                                <span class="badge badge-cancelled">ยกเลิก</span>
                            @endif
                        </td>
                        <td style="font-size: 0.775rem; color: var(--text-muted); white-space: nowrap;">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                ดูผลการซ่อม
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted); font-size: 0.825rem;">
                            คุณยังไม่มีรายการแจ้งซ่อมอุปกรณ์ในระบบ<br>
                            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm" style="margin-top: 0.75rem; font-size: 0.75rem;">
                                + แจ้งซ่อมอุปกรณ์ชิ้นแรก
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
