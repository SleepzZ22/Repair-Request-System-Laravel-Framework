@extends('layouts.app')

@section('title', 'รายละเอียดใบแจ้งซ่อม #' . $ticket->ticket_no)

@section('content')
<div style="max-width: 1050px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem;">
        <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none;">หน้าแรก</a> &gt; 
        @if(Auth::user()?->isUser())
            <a href="{{ route('tickets.myTickets') }}" style="color: var(--text-muted); text-decoration: none;">งานของฉัน</a> &gt;
        @else
            <a href="{{ route('tickets.index') }}" style="color: var(--text-muted); text-decoration: none;">รายการแจ้งซ่อม</a> &gt;
        @endif
        <span style="color: var(--text-main); font-weight: 600;">{{ $ticket->ticket_no }}</span>
    </div>

    <!-- Header -->
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <div class="page-title">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <h1>ใบแจ้งซ่อม {{ $ticket->ticket_no }}</h1>
                @if($ticket->status === 'pending')
                    <span class="badge badge-pending">รอดำเนินการ</span>
                @elseif($ticket->status === 'in_progress')
                    <span class="badge badge-in_progress">กำลังตรวจซ่อม</span>
                @elseif($ticket->status === 'waiting_parts')
                    <span class="badge badge-waiting_parts">รออะไหล่</span>
                @elseif($ticket->status === 'completed')
                    <span class="badge badge-completed">ซ่อมเสร็จสิ้นแล้ว</span>
                @else
                    <span class="badge badge-cancelled">ยกเลิกคำขอ</span>
                @endif

                @if($ticket->priority === 'urgent')
                    <span class="priority-pill priority-urgent">🔴 ด่วนที่สุด</span>
                @elseif($ticket->priority === 'high')
                    <span class="priority-pill priority-high">🟠 ด่วน</span>
                @else
                    <span class="priority-pill priority-normal">🔵 ปกติ</span>
                @endif
            </div>
            <p>แจ้งเมื่อ {{ $ticket->created_at->format('d/m/Y H:i') }} โดย {{ $ticket->reporter_name }} ({{ $ticket->department }})</p>
        </div>

        <div class="page-actions" style="display: flex; gap: 0.5rem;">
            <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm">
                🖨️ พิมพ์ใบงาน
            </button>
            @if(Auth::user()?->isUser())
                <a href="{{ route('tickets.myTickets') }}" class="btn btn-secondary btn-sm">&larr; ย้อนกลับ</a>
            @else
                <a href="{{ route('tickets.index') }}" class="btn btn-secondary btn-sm">&larr; ย้อนกลับ</a>
            @endif
        </div>
    </div>

    <!-- Visual Progress Stepper -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="font-size: 0.95rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
            ขั้นตอนการดำเนินงาน (Progress Stepper)
        </h3>

        @php
            $steps = ['pending' => 1, 'in_progress' => 2, 'waiting_parts' => 3, 'completed' => 4];
            $currentStepIndex = $steps[$ticket->status] ?? 1;
            $percent = (($currentStepIndex - 1) / 3) * 100;
        @endphp

        <div class="status-stepper">
            <div class="stepper-progress" style="width: {{ $percent }}%;"></div>

            <div class="step-node {{ $currentStepIndex >= 1 ? ($currentStepIndex > 1 ? 'completed' : 'active') : '' }}">
                <div class="step-circle">1</div>
                <div class="step-title">แจ้งเรื่องเข้าระบบ</div>
                <div class="step-date">เสร็จสมบูรณ์</div>
            </div>

            <div class="step-node {{ $currentStepIndex >= 2 ? ($currentStepIndex > 2 ? 'completed' : 'active') : '' }}">
                <div class="step-circle">2</div>
                <div class="step-title">รับเรื่อง / จัดสรรช่าง</div>
                <div class="step-date">{{ $currentStepIndex >= 2 ? ($ticket->technician ? $ticket->technician->name : 'จัดสรรแล้ว') : 'รอดำเนินการ' }}</div>
            </div>

            <div class="step-node {{ $currentStepIndex >= 3 ? ($currentStepIndex > 3 ? 'completed' : 'active') : '' }}">
                <div class="step-circle">3</div>
                <div class="step-title">ดำเนินการตรวจซ่อม</div>
                <div class="step-date">{{ $currentStepIndex >= 3 ? 'กำลังแก้ไข' : 'รอดำเนินการ' }}</div>
            </div>

            <div class="step-node {{ $currentStepIndex >= 4 ? 'completed active' : '' }}">
                <div class="step-circle">4</div>
                <div class="step-title">เสร็จสิ้น / ส่งมอบ</div>
                <div class="step-date">{{ $currentStepIndex >= 4 ? 'ปิดงานแล้ว' : 'ขั้นตอนสุดท้าย' }}</div>
            </div>
        </div>
    </div>

    <!-- 2 Column Details Layout -->
    <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 1.5rem; align-items: start;">
        <!-- Left: Issue Details & Evidence -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card">
                <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">
                    {{ $ticket->title }}
                </h2>
                <div style="display: flex; gap: 1rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    <span>หมวดหมู่: <strong>{{ $ticket->category }}</strong></span>
                    @if(!empty($ticket->asset_code))
                        <span>รหัสทรัพย์สิน: <code>{{ $ticket->asset_code }}</code></span>
                    @endif
                </div>

                <div style="background: var(--bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.4rem;">รายละเอียดอาการที่พบ:</h4>
                    <p style="white-space: pre-line; font-size: 0.925rem; color: #334155; line-height: 1.7;">{{ $ticket->description }}</p>
                </div>

                @if(!empty($ticket->repair_notes))
                    <div style="background: #ecfdf5; border: 1px solid #a7f3d0; padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1.25rem;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #065f46; margin-bottom: 0.4rem;">ผลการซ่อมและวิธีแก้ไขของช่าง:</h4>
                        <p style="white-space: pre-line; font-size: 0.925rem; color: #047857; line-height: 1.7;">{{ $ticket->repair_notes }}</p>
                    </div>
                @endif

                @if(!empty($ticket->images) && is_array($ticket->images) && count($ticket->images) > 0)
                    <div>
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem;">รูปภาพแนบประกอบ:</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                            @foreach($ticket->images as $img)
                                <a href="{{ $img }}" target="_blank" style="display: block; width: 110px; height: 110px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--border);">
                                    <img src="{{ $img }}" alt="รูปปัญหา" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Activity Logs -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">
                    ประวัติการดำเนินงาน (Activity Timeline)
                </h3>

                <div style="position: relative; padding-left: 1.5rem; border-left: 2px solid var(--border); display: flex; flex-direction: column; gap: 1.5rem;">
                    @forelse($ticket->logs as $log)
                        <div style="position: relative;">
                            <div style="position: absolute; left: -1.85rem; top: 2px; width: 12px; height: 12px; border-radius: 50%; background: var(--primary); border: 2px solid white; box-shadow: 0 0 0 2px var(--primary-glow);"></div>
                            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                                <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">{{ $log->action }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">
                                โดย: <span style="font-weight: 500; color: var(--text-main);">{{ $log->by_name }}</span>
                            </div>
                            @if(!empty($log->note))
                                <div style="font-size: 0.85rem; background: var(--bg-subtle); padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); margin-top: 0.4rem; color: #475569;">
                                    "{{ $log->note }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="color: var(--text-muted); font-size: 0.875rem;">ยังไม่มีบันทึกประวัติเพิ่มเติม</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Status & Action Panels based on Role -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Reporter & Location Card -->
            <div class="card">
                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: var(--text-main);">
                    ข้อมูลผู้แจ้ง & สถานที่
                </h3>

                <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.875rem;">
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;">ผู้แจ้งซ่อม:</div>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $ticket->reporter_name }}</div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;">แผนก / ฝ่าย:</div>
                        <div style="font-weight: 500;">{{ $ticket->department }}</div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;">เบอร์โทรติดต่อ:</div>
                        <div style="font-weight: 600; color: var(--primary);">
                            <a href="tel:{{ $ticket->phone }}" style="color: inherit; text-decoration: none;">📞 {{ $ticket->phone }}</a>
                        </div>
                    </div>
                    <div>
                        <div style="color: var(--text-muted); font-size: 0.75rem;">สถานที่ตั้งเครื่อง / ห้อง:</div>
                        <div style="font-weight: 500;">📍 {{ $ticket->location }}</div>
                    </div>
                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                        <div style="color: var(--text-muted); font-size: 0.75rem;">ช่างผู้รับผิดชอบ:</div>
                        @if($ticket->technician)
                            <div style="font-weight: 700; color: var(--primary); font-size: 0.95rem;">
                                🛠️ {{ $ticket->technician->name }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $ticket->technician->specialty ?? 'ช่างไอที' }} • {{ $ticket->technician->phone }}
                            </div>
                        @else
                            <div style="color: #b45309; font-weight: 600; font-size: 0.9rem;">
                                ⏳ ยังไม่มีผู้รับผิดชอบ
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Role Action: TECHNICIAN CLAIM ACTION (ช่างกดรับงานเองได้ทันที) -->
            @if(Auth::user()?->isTechnician() && empty($ticket->technician_id))
                <div class="card" style="border: 2px solid #0284c7; background: #f0f9ff;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #0369a1; margin-bottom: 0.5rem;">
                        ⚡ รับงานนี้เข้าคิวของคุณ
                    </h3>
                    <p style="font-size: 0.8rem; color: #0284c7; margin-bottom: 1rem;">
                        งานนี้ยังไม่มีช่างดูแล คุณสามารถกดรับงานเพื่อเริ่มดำเนินการได้ทันที
                    </p>
                    <form action="{{ route('technician.claim', $ticket->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                            ⚡ กดรับงานนี้ทันที (Claim Ticket)
                        </button>
                    </form>
                </div>
            @endif

            <!-- Role Action: ADMIN ASSIGN PANEL (แอดมินมอบหมายงานให้ช่าง 1 ใน 4 คน) -->
            @if(Auth::user()?->isAdmin())
                <div class="card" style="border: 2px solid #6366f1; background: #fdfdfe;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        👑 มอบหมายงานให้ช่าง (Admin Action)
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                        เลือกช่าง 1 ใน 4 คนเพื่อมอบหมายให้รับผิดชอบใบงานนี้
                    </p>

                    <form action="{{ route('admin.assign', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="technician_id">เลือกช่างผู้รับผิดชอบ:</label>
                            <select id="technician_id" name="technician_id" class="form-select" required>
                                <option value="">-- เลือกช่างประจำทีม 4 คน --</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ $ticket->technician_id == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }} ({{ $tech->specialty ?? 'ช่างไอที' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            {{ $ticket->technician_id ? 'เปลี่ยนผู้รับผิดชอบ' : 'ยืนยันการมอบหมายงาน' }}
                        </button>
                    </form>
                </div>
            @endif

            <!-- Role Action: UPDATE STATUS PANEL (สำหรับช่างที่รับผิดชอบ หรือ แอดมิน) -->
            @if(Auth::user()?->isAdmin() || Auth::user()?->isTechnician())
                <div class="card" style="border: 2px solid var(--border);">
                    <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-main);">
                        🛠️ บันทึกผลและอัปเดตสถานะ
                    </h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">
                        เปลี่ยนสถานะของงาน และบันทึกรายละเอียดขั้นตอนการซ่อม
                    </p>

                    <form action="{{ route('tickets.updateStatus', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <label class="form-label" for="status">สถานะงานใหม่:</label>
                            <select id="status" name="status" class="form-select">
                                <option value="pending" {{ $ticket->status == 'pending' ? 'selected' : '' }}>รอดำเนินการ (Pending)</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการซ่อม (In Progress)</option>
                                <option value="waiting_parts" {{ $ticket->status == 'waiting_parts' ? 'selected' : '' }}>รออะไหล่ / ส่งซ่อมภายนอก (Waiting Parts)</option>
                                <option value="completed" {{ $ticket->status == 'completed' ? 'selected' : '' }}>ซ่อมเสร็จสิ้น / ปิดงาน (Completed)</option>
                                <option value="cancelled" {{ $ticket->status == 'cancelled' ? 'selected' : '' }}>ยกเลิกใบงาน (Cancelled)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="note">บันทึกขั้นตอนการซ่อม / วิธีการแก้ไข:</label>
                            <textarea id="note" name="note" class="form-control" style="min-height: 85px;" placeholder="ระบุสิ่งที่ได้ตรวจเช็ค หรือชิ้นส่วนที่เปลี่ยน เพื่อเป็นประวัติการซ่อม...">{{ $ticket->repair_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            บันทึกการอัปเดตสถานะ
                        </button>
                    </form>
                </div>
            @endif

            <!-- Normal User Notice -->
            @if(Auth::user()?->isUser())
                <div class="card" style="background: #f8fafc; border: 1px dashed var(--border); text-align: center; padding: 1.25rem;">
                    <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">💬</div>
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--text-main);">ต้องการสอบถามเพิ่มเติม?</h4>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        หากงานซ่อมมีความเร่งด่วนเป็นพิเศษ สามารถติดต่อช่างที่ดูแล หรือฝ่าย IT Helpdesk ได้โดยตรง
                    </p>
                    <a href="tel:021234500" class="btn btn-secondary btn-sm" style="width: 100%;">
                        📞 ติดต่อสายด่วน IT (ต่อ 901)
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
