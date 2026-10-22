@extends('layouts.app')

@section('title', 'ติดตามสถานะงานแจ้งซ่อม')

@section('content')
<div style="max-width: 800px; margin: 1rem auto;">
    <!-- Tracking Card -->
    <div class="card" style="text-align: center; padding: 2.5rem 2rem; margin-bottom: 2rem;">
        <div style="width: 56px; height: 56px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; margin: 0 auto 1.25rem;">
            🔍
        </div>
        <h1 style="font-size: 1.6rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">
            ติดตามสถานะงานแจ้งซ่อมอุปกรณ์
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto 1.75rem;">
            กรอกหมายเลขใบแจ้งซ่อม (เช่น TK-202610-001) หรือเบอร์โทรศัพท์ของผู้แจ้ง เพื่อตรวจสอบความคืบหน้าแบบเรียลไทม์
        </p>

        <form action="{{ route('tickets.track') }}" method="GET" style="display: flex; gap: 0.5rem; max-width: 560px; margin: 0 auto;">
            <input type="text" name="query" class="form-control" style="font-size: 1rem; padding: 0.85rem 1.25rem; border-radius: var(--radius-full);" placeholder="ใส่รหัสแจ้งซ่อม หรือ เบอร์โทรศัพท์..." value="{{ request('query') }}" required autofocus>
            <button type="submit" class="btn btn-primary btn-lg" style="border-radius: var(--radius-full); padding: 0.85rem 1.75rem;">
                ค้นหาสถานะ
            </button>
        </form>
    </div>

    <!-- Search Result Section (if searched) -->
    @if(isset($resultTicket) && $resultTicket)
        <div class="card" style="border: 2px solid var(--primary-light); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">ผลการค้นหา:</span>
                    <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--primary);">
                        {{ $resultTicket['ticket_no'] }}
                    </h2>
                </div>
                <div>
                    @if($resultTicket['status'] === 'pending')
                        <span class="badge badge-pending">รอดำเนินการ</span>
                    @elseif($resultTicket['status'] === 'in_progress')
                        <span class="badge badge-in_progress">กำลังตรวจซ่อม</span>
                    @elseif($resultTicket['status'] === 'waiting_parts')
                        <span class="badge badge-waiting_parts">รออะไหล่</span>
                    @elseif($resultTicket['status'] === 'completed')
                        <span class="badge badge-completed">ซ่อมเสร็จสิ้น</span>
                    @else
                        <span class="badge badge-cancelled">ยกเลิก</span>
                    @endif
                </div>
            </div>

            <!-- Stepper -->
            @php
                $status = $resultTicket['status'] ?? 'pending';
                $hasTech = !empty($resultTicket['technician_id']) || !empty($resultTicket['technician_name']);
                if ($status === 'completed') {
                    $currentStepIndex = 4;
                } elseif (in_array($status, ['in_progress', 'waiting_parts'])) {
                    $currentStepIndex = 3;
                } elseif ($hasTech) {
                    $currentStepIndex = 2;
                } else {
                    $currentStepIndex = 1;
                }
                $percent = (($currentStepIndex - 1) / 3) * 100;
            @endphp
            <div class="status-stepper" style="margin: 2rem 0;">
                <div class="stepper-progress" style="width: {{ $percent }}%;"></div>
                <div class="step-node {{ $currentStepIndex > 1 ? 'completed' : ($currentStepIndex == 1 ? 'active' : '') }}">
                    <div class="step-circle">1</div>
                    <div class="step-title">แจ้งเรื่อง</div>
                </div>
                <div class="step-node {{ $currentStepIndex > 2 ? 'completed' : ($currentStepIndex == 2 ? 'active' : '') }}">
                    <div class="step-circle">2</div>
                    <div class="step-title">รับเรื่องตรวจสอบ</div>
                </div>
                <div class="step-node {{ $currentStepIndex > 3 ? 'completed' : ($currentStepIndex == 3 ? 'active' : '') }}">
                    <div class="step-circle">3</div>
                    <div class="step-title">ดำเนินการซ่อม</div>
                </div>
                <div class="step-node {{ $currentStepIndex >= 4 ? 'completed active' : '' }}">
                    <div class="step-circle">4</div>
                    <div class="step-title">เสร็จสิ้นส่งมอบ</div>
                </div>
            </div>

            <!-- Details -->
            <div style="background: var(--bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); margin-top: 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem;">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">อุปกรณ์ / ปัญหา:</span>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $resultTicket['title'] }}</div>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">ผู้แจ้ง / แผนก:</span>
                    <div style="font-weight: 600; color: var(--text-main);">{{ $resultTicket['reporter_name'] }} ({{ $resultTicket['department'] }})</div>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">สถานที่ตั้ง:</span>
                    <div>📍 {{ $resultTicket['location'] }}</div>
                </div>
                <div>
                    <span style="color: var(--text-muted); font-size: 0.8rem;">ช่างผู้รับผิดชอบ:</span>
                    <div style="font-weight: 600; color: var(--primary);">
                        {{ $resultTicket['technician_name'] ?? 'กำลังรอจัดสรรช่าง' }}
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.5rem; text-align: right;">
                <a href="{{ route('tickets.show', $resultTicket['id']) }}" class="btn btn-primary btn-sm">
                    ดูรายละเอียดเต็ม &rarr;
                </a>
            </div>
        </div>
    @elseif(request()->filled('query'))
        <div class="alert alert-danger" style="text-align: center; justify-content: center;">
            ไม่พบข้อมูลใบแจ้งซ่อมตามคำค้นหา "{{ request('query') }}" กรุณาตรวจสอบรหัสหรือเบอร์โทรศัพท์อีกครั้ง
        </div>
    @endif
</div>
@endsection
