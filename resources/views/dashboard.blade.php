@extends('layouts.app')

@section('title', 'แดชบอร์ดภาพรวม')

@section('content')
<!-- Page Title -->
<div class="page-header">
    <div class="page-title">
        <h1>แดชบอร์ดระบบแจ้งซ่อมไอที</h1>
        <p>ภาพรวมสถานะการแจ้งซ่อมและงานบริการไอทีประจำวันนี้</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 0.75rem;">
        <a href="{{ route('tickets.track') }}" class="btn btn-secondary">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            ค้นหาสถานะงานซ่อม
        </a>
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

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-info">
            <p>งานแจ้งซ่อมทั้งหมด</p>
            <h3>{{ $stats['total'] ?? 24 }}</h3>
        </div>
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
        </div>
    </div>

    <div class="stat-card stat-warning">
        <div class="stat-info">
            <p>รอดำเนินการ / รับเรื่อง</p>
            <h3>{{ $stats['pending'] ?? 6 }}</h3>
        </div>
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
    </div>

    <div class="stat-card stat-info-theme">
        <div class="stat-info">
            <p>กำลังตรวจซ่อม</p>
            <h3>{{ $stats['in_progress'] ?? 5 }}</h3>
        </div>
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
            </svg>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-info">
            <p>ซ่อมเสร็จสิ้นแล้ว</p>
            <h3>{{ $stats['completed'] ?? 13 }}</h3>
        </div>
        <div class="stat-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
    </div>
</div>

<!-- Main Section: Recent Tickets & Category Summary -->
<div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- Recent Tickets Card -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">รายการแจ้งซ่อมล่าสุด</h2>
                <p style="font-size: 0.8rem; color: var(--text-muted);">รายการปัญหาอุปกรณ์ที่พนักงานแจ้งเข้ามาในระบบ</p>
            </div>
            
            <!-- Filter Tabs -->
            <div style="display: flex; gap: 0.35rem; background: var(--bg-subtle); padding: 0.25rem; border-radius: var(--radius-md);">
                <button type="button" class="btn btn-sm filter-tab-btn active" data-filter="all" style="border: none;">ทั้งหมด</button>
                <button type="button" class="btn btn-sm filter-tab-btn" data-filter="pending" style="border: none;">รอดำเนินการ</button>
                <button type="button" class="btn btn-sm filter-tab-btn" data-filter="in_progress" style="border: none;">กำลังซ่อม</button>
                <button type="button" class="btn btn-sm filter-tab-btn" data-filter="completed" style="border: none;">เสร็จสิ้น</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>รหัสงาน</th>
                        <th>อุปกรณ์ / ปัญหาที่พบ</th>
                        <th>ผู้แจ้ง / แผนก</th>
                        <th>ความเร่งด่วน</th>
                        <th>สถานะ</th>
                        <th>วันที่แจ้ง</th>
                        <th style="text-align: right;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="ticket-table-row" data-status="{{ $ticket['status'] }}">
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
                            <td>
                                <div style="font-weight: 500;">{{ $ticket['reporter_name'] }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $ticket['department'] }}</div>
                            </td>
                            <td>
                                @if($ticket['priority'] === 'urgent')
                                    <span class="priority-pill priority-urgent">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                        ด่วนที่สุด
                                    </span>
                                @elseif($ticket['priority'] === 'high')
                                    <span class="priority-pill priority-high">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                        ด่วน
                                    </span>
                                @else
                                    <span class="priority-pill priority-normal">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                        ปกติ
                                    </span>
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
                                <a href="{{ route('tickets.show', $ticket['id']) }}" class="btn btn-secondary btn-sm" title="ดูรายละเอียด">
                                    รายละเอียด
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                ยังไม่มีข้อมูลการแจ้งซ่อมในระบบ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.25rem; text-align: center;">
            <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm">
                ดูประวัติการแจ้งซ่อมทั้งหมด &rarr;
            </a>
        </div>
    </div>

    <!-- Right Column: Categories & Support Hotline -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Categories Widget -->
        <div class="card">
            <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">
                หมวดหมู่อุปกรณ์ยอดนิยม
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: var(--bg-subtle); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.35rem;">💻</span>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">คอมพิวเตอร์ / โน้ตบุ๊ก</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">PC, Laptop, Mac</div>
                        </div>
                    </div>
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">12 งาน</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: var(--bg-subtle); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.35rem;">🖨️</span>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">เครื่องพิมพ์ / สแกนเนอร์</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Printer, Scanner, Copier</div>
                        </div>
                    </div>
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">5 งาน</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: var(--bg-subtle); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.35rem;">🌐</span>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">ระบบเครือข่าย / อินเทอร์เน็ต</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Wi-Fi, LAN, VPN</div>
                        </div>
                    </div>
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">4 งาน</span>
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 0.75rem; background: var(--bg-subtle); border-radius: var(--radius-md);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.35rem;">💾</span>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">โปรแกรม / อีเมล / บัญชีผู้ใช้</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Windows, Office, Password</div>
                        </div>
                    </div>
                    <span style="font-weight: 700; font-size: 0.9rem; color: var(--primary);">3 งาน</span>
                </div>
            </div>
        </div>

        <!-- Support Assistance Card -->
        <div class="card" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); color: white; border: none;">
            <div style="display: flex; align-items: flex-start; gap: 0.85rem;">
                <div style="width: 44px; height: 44px; background: rgba(255,255,255,0.18); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    📞
                </div>
                <div>
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.25rem;">กรณีปัญหาฉุกเฉินเร่งด่วน</h3>
                    <p style="font-size: 0.8rem; opacity: 0.9; margin-bottom: 0.75rem;">
                        หากเซิร์ฟเวอร์หลักหรือระบบเครือข่ายล่ม สามารถติดต่อสายตรงทีม IT Helpdesk ได้ทันที
                    </p>
                    <div style="font-size: 0.95rem; font-weight: 700; background: rgba(255,255,255,0.15); padding: 0.45rem 0.85rem; border-radius: var(--radius-sm); display: inline-block;">
                        โทรภายใน: 1999 หรือ 02-xxx-xxxx
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
