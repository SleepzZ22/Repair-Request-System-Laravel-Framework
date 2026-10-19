<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@it.local'],
            [
                'name' => 'อัครเดช บริหารระบบ (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department' => 'ฝ่ายเทคโนโลยีสารสนเทศ (IT)',
                'phone' => '02-123-4500 ต่อ 901',
                'specialty' => 'ผู้ดูแลระบบและจัดสรรงาน',
            ]
        );

        // 2. Create the 4 Technicians
        $tech1 = User::updateOrCreate(
            ['email' => 'tech1@it.local'],
            [
                'name' => 'สมศักดิ์ สุขใจ (ช่าง 1)',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'department' => 'ทีมบริการเทคนิค (IT Support)',
                'phone' => '081-111-0001',
                'specialty' => 'คอมพิวเตอร์ / โน้ตบุ๊ก / ฮาร์ดแวร์',
            ]
        );

        $tech2 = User::updateOrCreate(
            ['email' => 'tech2@it.local'],
            [
                'name' => 'อนุชา ว่องไว (ช่าง 2)',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'department' => 'ทีมบริการเทคนิค (IT Support)',
                'phone' => '081-222-0002',
                'specialty' => 'ระบบเครือข่าย / อินเทอร์เน็ต / Wi-Fi',
            ]
        );

        $tech3 = User::updateOrCreate(
            ['email' => 'tech3@it.local'],
            [
                'name' => 'ธีรเดช เก่งงาน (ช่าง 3)',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'department' => 'ทีมบริการเทคนิค (IT Support)',
                'phone' => '081-333-0003',
                'specialty' => 'เครื่องพิมพ์ / สแกนเนอร์ / โปรเจกเตอร์',
            ]
        );

        $tech4 = User::updateOrCreate(
            ['email' => 'tech4@it.local'],
            [
                'name' => 'กิตติศักดิ์ ชำนาญ (ช่าง 4)',
                'password' => Hash::make('password'),
                'role' => 'technician',
                'department' => 'ทีมบริการเทคนิค (IT Support)',
                'phone' => '081-444-0004',
                'specialty' => 'โปรแกรม / อีเมล / วินโดวส์ / บัญชีผู้ใช้',
            ]
        );

        // 3. Create General Users (Employees)
        $user1 = User::updateOrCreate(
            ['email' => 'user@company.local'],
            [
                'name' => 'คุณพรทิพย์ สุวรรณดี',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'บัญชีและการเงิน',
                'phone' => '089-123-4567',
                'specialty' => null,
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'user2@company.local'],
            [
                'name' => 'คุณธนากร ใจมั่น',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'ทรัพยากรบุคคล (HR)',
                'phone' => '082-998-8776',
                'specialty' => null,
            ]
        );

        // 4. Seed Initial Tickets
        TicketLog::query()->delete();
        Ticket::query()->delete();

        // Ticket 1: In progress with Tech 1 (สมศักดิ์)
        $t1 = Ticket::create([
            'ticket_no' => 'TK-202610-001',
            'title' => 'หน้าจอคอมพิวเตอร์เปิดไม่ติด มีไฟสีส้มกะพริบ',
            'category' => 'คอมพิวเตอร์ / โน้ตบุ๊ก',
            'asset_code' => 'AST-PC-2024-041',
            'location' => 'อาคาร A ชั้น 3 ห้องบัญชี โต๊ะ 02',
            'priority' => 'high',
            'description' => 'เปิดเครื่องตอนเช้าแล้วหน้าจอไม่มีภาพขึ้น มีไฟกระพริบสีส้มที่ปุ่มเปิดจอ ได้ลองขยับสาย VGA และสายไฟด้านหลังแล้วยังไม่ติดครับ มีงานด่วนต้องปิดงบสิ้นเดือน',
            'user_id' => $user1->id,
            'reporter_name' => $user1->name,
            'department' => $user1->department,
            'phone' => $user1->phone,
            'technician_id' => $tech1->id,
            'technician_name' => $tech1->name,
            'status' => 'in_progress',
        ]);

        TicketLog::create([
            'ticket_id' => $t1->id,
            'user_id' => $user1->id,
            'action' => 'แจ้งเรื่องเข้าระบบสำเร็จ',
            'by_name' => $user1->name,
            'note' => 'เปิดใบแจ้งซ่อมใหม่ผ่านระบบออนไลน์'
        ]);
        TicketLog::create([
            'ticket_id' => $t1->id,
            'user_id' => $admin->id,
            'action' => 'แอดมินมอบหมายงานให้ช่าง',
            'by_name' => $admin->name,
            'note' => 'มอบหมายงานให้ ' . $tech1->name . ' เข้าตรวจสอบหน้างาน'
        ]);

        // Ticket 2: Pending (No technician assigned yet -> Technicians can claim this!)
        $t2 = Ticket::create([
            'ticket_no' => 'TK-202610-002',
            'title' => 'เครื่องพิมพ์ HP LaserJet กระดาษติดด้านในดึงไม่ออก',
            'category' => 'เครื่องพิมพ์ / สแกนเนอร์',
            'asset_code' => 'AST-PRN-2023-112',
            'location' => 'อาคาร B ชั้น 2 แผนกบุคคล (HR)',
            'priority' => 'normal',
            'description' => 'พิมพ์เอกสารสัญญาแล้วเกิดเสียงดังแกร๊กๆ จากนั้นเครื่องหยุดทำงาน ขึ้นไฟสีแดงเตือนกระดาษติด พยายามเปิดฝาด้านหน้าดูแล้วแต่กระดาษม้วนเข้าไปลึก ไม่กล้าดึงแรงกลัวลูกยางขาด',
            'user_id' => $user2->id,
            'reporter_name' => $user2->name,
            'department' => $user2->department,
            'phone' => $user2->phone,
            'technician_id' => null,
            'technician_name' => null,
            'status' => 'pending',
        ]);

        TicketLog::create([
            'ticket_id' => $t2->id,
            'user_id' => $user2->id,
            'action' => 'แจ้งเรื่องเข้าระบบสำเร็จ',
            'by_name' => $user2->name,
            'note' => 'รอมอบหมายช่าง หรือ ช่างสามารถกดรับงานเองได้'
        ]);

        // Ticket 3: Urgent Network issue assigned to Tech 2 (อนุชา)
        $t3 = Ticket::create([
            'ticket_no' => 'TK-202610-003',
            'title' => 'อินเทอร์เน็ต Wi-Fi หลุดบ่อย เชื่อมต่อได้แต่ใช้งานไม่ได้',
            'category' => 'เครือข่าย / อินเทอร์เน็ต',
            'asset_code' => null,
            'location' => 'ห้องประชุมใหญ่ ชั้น 4',
            'priority' => 'urgent',
            'description' => 'มีนัดประชุมออนไลน์กับลูกค้าบ่ายโมง สัญญาณ Wi-Fi ขึ้นชื่อแต่กด Connect แล้วขึ้น No Internet Access หรือหลุดทุกๆ 5 นาที รบกวนช่วยตรวจสอบ Access Point ด่วนครับ',
            'user_id' => $user1->id,
            'reporter_name' => 'คุณวิชัย พัฒนา',
            'department' => 'ฝ่ายขายและการตลาด',
            'phone' => '081-555-4321',
            'technician_id' => $tech2->id,
            'technician_name' => $tech2->name,
            'status' => 'in_progress',
        ]);

        TicketLog::create([
            'ticket_id' => $t3->id,
            'user_id' => $tech2->id,
            'action' => 'ช่างกดรับงานเอง (Claim Ticket)',
            'by_name' => $tech2->name,
            'note' => 'ช่างอนุชา รับงานตรวจสอบระบบ Wi-Fi ห้องประชุมใหญ่'
        ]);

        // Ticket 4: Completed software task by Tech 4 (กิตติศักดิ์)
        $t4 = Ticket::create([
            'ticket_no' => 'TK-202610-004',
            'title' => 'ลงโปรแกรม Microsoft Excel และ Outlook สำหรับพนักงานใหม่',
            'category' => 'โปรแกรม / บัญชีผู้ใช้',
            'asset_code' => 'AST-NB-2025-015',
            'location' => 'อาคาร A ชั้น 2 ฝ่ายจัดซื้อ',
            'priority' => 'normal',
            'description' => 'พนักงานใหม่เริ่มงานวันนี้ ต้องการติดตั้ง MS Office 365 และตั้งค่าอีเมลองค์กรบนโน้ตบุ๊กประจำตำแหน่ง',
            'user_id' => $user2->id,
            'reporter_name' => 'คุณอรทัย ชื่นใจ',
            'department' => 'ฝ่ายจัดซื้อและคลังสินค้า',
            'phone' => '087-654-3210',
            'technician_id' => $tech4->id,
            'technician_name' => $tech4->name,
            'status' => 'completed',
            'repair_notes' => 'ติดตั้ง Office 365 ผูก License เรียบร้อย ทดสอบเปิดใช้งานปกติ',
        ]);

        TicketLog::create([
            'ticket_id' => $t4->id,
            'user_id' => $tech4->id,
            'action' => 'ปิดงานซ่อม / ส่งมอบเรียบร้อย',
            'by_name' => $tech4->name,
            'note' => 'ติดตั้ง Office 365 ผูก License และผู้ใช้ตรวจรับเรียบร้อยแล้ว'
        ]);
    }
}
