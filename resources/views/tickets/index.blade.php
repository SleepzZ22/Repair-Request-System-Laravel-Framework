@extends('layouts.app')

@section('title', 'รายการแจ้งซ่อมทั้งหมด')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>รายการแจ้งซ่อมทั้งหมด</h1>
        <p>ค้นหา ตรวจสอบ และติดตามสถานะงานแจ้งซ่อมทั้งหมดในองค์กร</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            เปิดใบแจ้งซ่อมใหม่
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
    <form action="{{ route('tickets.index') }}" method="GET" style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 0.75rem; align-items: end;">
        <div>
            <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">คำค้นหา</label>
            <input type="text" name="search" class="form-control" placeholder="ค้นหารหัสงาน, อุปกรณ์, หรือชื่อผู้แจ้ง..." value="{{ request('search') }}">
        </div>

        <div>
            <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">สถานะงาน</label>
            <select name="status" class="form-select">
                <option value="">-- ทุกสถานะ --</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>กำลังซ่อม</option>
                <option value="waiting_parts" {{ request('status') == 'waiting_parts' ? 'selected' : '' }}>รออะไหล่</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
            </select>
        </div>

        <div>
            <label class="form-label" style="font-size: 0.8rem; margin-bottom: 0.25rem;">ความเร่งด่วน</label>
            <select name="priority" class="form-select">
                <option value="">-- ทุกระดับ --</option>
                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>ปกติ</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>ด่วน</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>ด่วนที่สุด</option>
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-secondary" style="height: 42px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                กรองข้อมูล
            </button>
        </div>
    </form>
</div>

<!-- Tickets Table Card -->
<div class="card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>รหัสงาน</th>
                    <th>อุปกรณ์ / หัวข้อปัญหา</th>
                    <th>สถานที่</th>
                    <th>ผู้แจ้ง / แผนก</th>
                    <th>ช่างผู้ดูแล</th>
                    <th>ระดับความเร่งด่วน</th>
                    <th>สถานะ</th>
                    <th>วันที่แจ้ง</th>
                    <th style="text-align: right;">การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr>
                        <td>
                            <a href="{{ route('tickets.show', $ticket['id']) }}" class="ticket-id">
                                {{ $ticket['ticket_no'] }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $ticket['title'] }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem;">
                                <span>{{ $ticket['category'] }}</span>
                                @if(!empty($ticket['asset_code']))
                                    • <code>{{ $ticket['asset_code'] }}</code>
                                @endif
                            </div>
                        </td>
                        <td style="font-size: 0.825rem; color: var(--text-muted);">
                            📍 {{ $ticket['location'] }}
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $ticket['reporter_name'] }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $ticket['department'] }}</div>
                        </td>
                        <td>
                            @if(!empty($ticket['technician_name']))
                                <span style="font-size: 0.825rem; font-weight: 500; color: var(--text-main);">
                                    🛠️ {{ $ticket['technician_name'] }}
                                </span>
                            @else
                                <span style="font-size: 0.75rem; color: var(--text-subtle);">- ยังไม่ได้มอบหมาย -</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket['priority'] === 'urgent')
                                <span class="priority-pill priority-urgent">🔴 ด่วนที่สุด</span>
                            @elseif($ticket['priority'] === 'high')
                                <span class="priority-pill priority-high">🟠 ด่วน</span>
                            @else
                                <span class="priority-pill priority-normal">🔵 ปกติ</span>
                            @endif
                        </td>
                        <td>
                            @if($ticket['status'] === 'pending')
                                <span class="badge badge-pending">รอดำเนินการ</span>
                            @elseif($ticket['status'] === 'in_progress')
                                <span class="badge badge-in_progress">กำลังซ่อม</span>
                            @elseif($ticket['status'] === 'waiting_parts')
                                <span class="badge badge-waiting_parts">รออะไหล่</span>
                            @elseif($ticket['status'] === 'completed')
                                <span class="badge badge-completed">ซ่อมเสร็จสิ้น</span>
                            @else
                                <span class="badge badge-cancelled">ยกเลิก</span>
                            @endif
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-muted); white-space: nowrap;">
                            {{ $ticket['created_at'] }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('tickets.show', $ticket['id']) }}" class="btn btn-secondary btn-sm">
                                ดูข้อมูล
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            ไม่พบรายการแจ้งซ่อมที่ตรงกับเงื่อนไขการค้นหา
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
