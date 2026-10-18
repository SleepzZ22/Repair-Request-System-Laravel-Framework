@extends('layouts.app')

@section('title', 'แจ้งซ่อมอุปกรณ์ไอที')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">
        <a href="{{ url('/') }}" style="color: var(--text-muted); text-decoration: none;">หน้าแรก</a> &gt; 
        <span style="color: var(--text-main); font-weight: 500;">แจ้งซ่อมอุปกรณ์</span>
    </div>

    <!-- Header -->
    <div class="page-header" style="margin-bottom: 1.25rem;">
        <div class="page-title">
            <h1>แบบฟอร์มแจ้งซ่อมอุปกรณ์</h1>
            <p>กรอกข้อมูลปัญหาของอุปกรณ์เพื่อส่งเรื่องให้ทีมช่างไอทีเข้าตรวจสอบ</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <div>
                <strong>กรุณาตรวจสอบข้อมูลที่กรอก:</strong>
                <ul style="margin: 0.25rem 0 0 1rem; font-size: 0.8rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Section 1: Category & Priority -->
        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.85rem; color: var(--text-main);">
                1. เลือกประเภทอุปกรณ์และความเร่งด่วน
            </h2>

            <input type="hidden" name="category" id="selectedCategoryInput" value="{{ old('category', 'computer') }}">

            <div class="category-picker">
                <div class="category-card {{ old('category', 'computer') == 'computer' ? 'selected' : '' }}" data-value="computer">
                    <span class="icon">💻</span>
                    <span class="title">คอมพิวเตอร์ / โน้ตบุ๊ก</span>
                </div>
                <div class="category-card {{ old('category') == 'printer' ? 'selected' : '' }}" data-value="printer">
                    <span class="icon">🖨️</span>
                    <span class="title">เครื่องพิมพ์ / สแกนเนอร์</span>
                </div>
                <div class="category-card {{ old('category') == 'network' ? 'selected' : '' }}" data-value="network">
                    <span class="icon">🌐</span>
                    <span class="title">เครือข่าย / อินเทอร์เน็ต</span>
                </div>
                <div class="category-card {{ old('category') == 'software' ? 'selected' : '' }}" data-value="software">
                    <span class="icon">💾</span>
                    <span class="title">โปรแกรม / บัญชีผู้ใช้</span>
                </div>
                <div class="category-card {{ old('category') == 'other' ? 'selected' : '' }}" data-value="other">
                    <span class="icon">🔌</span>
                    <span class="title">อุปกรณ์พ่วง / อื่นๆ</span>
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <label class="form-label">ระดับความเร่งด่วน <span class="required">*</span></label>
                <input type="hidden" name="priority" id="selectedPriorityInput" value="{{ old('priority', 'normal') }}">

                <div class="priority-selector">
                    <div class="priority-btn {{ old('priority', 'normal') == 'normal' ? 'selected' : '' }}" data-val="normal">
                        ปกติ (ทั่วไป)
                    </div>
                    <div class="priority-btn {{ old('priority') == 'high' ? 'selected' : '' }}" data-val="high">
                        ด่วน (กระทบการทำงาน)
                    </div>
                    <div class="priority-btn {{ old('priority') == 'urgent' ? 'selected' : '' }}" data-val="urgent">
                        ด่วนที่สุด (ระบบหยุดชะงัก)
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Details of Issue -->
        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.85rem; color: var(--text-main);">
                2. รายละเอียดปัญหา
            </h2>

            <div class="form-group">
                <label class="form-label" for="title">หัวข้อแจ้งซ่อม / อาการเสียโดยสรุป <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-control" placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด, เครื่องพิมพ์กระดาษติด" value="{{ old('title') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" for="asset_code">รหัสทรัพย์สิน / หมายเลขเครื่อง (ถ้ามี)</label>
                    <input type="text" id="asset_code" name="asset_code" class="form-control" placeholder="เช่น AST-PC-2024-082" value="{{ old('asset_code') }}">
                </div>

                <div class="form-group">
                    <label class="form-label" for="location">สถานที่ตั้งอุปกรณ์ / ชั้น / ห้อง <span class="required">*</span></label>
                    <input type="text" id="location" name="location" class="form-control" placeholder="เช่น อาคาร A ชั้น 3 ห้องบัญชี" value="{{ old('location') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">รายละเอียดเพิ่มเติมของปัญหา <span class="required">*</span></label>
                <textarea id="description" name="description" class="form-control" placeholder="ระบุอาการอย่างละเอียด..." required>{{ old('description') }}</textarea>
            </div>

            <!-- Photos upload -->
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">แนบรูปถ่ายปัญหา (ถ้ามี)</label>
                <input type="file" id="imageFilesInput" name="images[]" multiple accept="image/*" style="display: none;">
                
                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">📷</div>
                    <div style="font-weight: 500; font-size: 0.825rem; color: var(--text-main);">คลิกเพื่อเลือกรูป หรือลากไฟล์มาวางที่นี่</div>
                    <p style="font-size: 0.75rem;">ไฟล์ JPG, PNG ขนาดไม่เกิน 5MB</p>
                </div>

                <div class="image-preview-container" id="imagePreviewContainer"></div>
            </div>
        </div>

        <!-- Section 3: Contact & Reporter Info -->
        <div class="card" style="margin-bottom: 1.25rem;">
            <h2 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.85rem; color: var(--text-main);">
                3. ข้อมูลผู้แจ้งซ่อม
            </h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                <div class="form-group">
                    <label class="form-label" for="reporter_name">ชื่อผู้แจ้ง <span class="required">*</span></label>
                    <input type="text" id="reporter_name" name="reporter_name" class="form-control" placeholder="ชื่อ-นามสกุล" value="{{ old('reporter_name', Auth::user()?->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="department">แผนก / ฝ่าย <span class="required">*</span></label>
                    <select id="department" name="department" class="form-select" required>
                        <option value="">-- เลือกแผนก --</option>
                        <option value="บัญชีและการเงิน" {{ old('department', Auth::user()?->department) == 'บัญชีและการเงิน' ? 'selected' : '' }}>บัญชีและการเงิน</option>
                        <option value="ทรัพยากรบุคคล (HR)" {{ old('department', Auth::user()?->department) == 'ทรัพยากรบุคคล (HR)' ? 'selected' : '' }}>ทรัพยากรบุคคล (HR)</option>
                        <option value="ฝ่ายขายและการตลาด" {{ old('department', Auth::user()?->department) == 'ฝ่ายขายและการตลาด' ? 'selected' : '' }}>ฝ่ายขายและการตลาด</option>
                        <option value="ฝ่ายจัดซื้อและคลังสินค้า" {{ old('department', Auth::user()?->department) == 'ฝ่ายจัดซื้อและคลังสินค้า' ? 'selected' : '' }}>ฝ่ายจัดซื้อและคลังสินค้า</option>
                        <option value="อื่นๆ" {{ old('department', Auth::user()?->department) == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">เบอร์ติดต่อ <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="เบอร์โทรศัพท์" value="{{ old('phone', Auth::user()?->phone) }}" required>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center;">
            <a href="{{ url('/') }}" class="btn btn-secondary">ยกเลิก</a>
            <button type="submit" class="btn btn-primary">
                ส่งใบแจ้งซ่อม
            </button>
        </div>
    </form>
</div>
@endsection
