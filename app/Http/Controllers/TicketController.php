<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Helper to get currently active user or fallback to first admin for guest browsing
     */
    private function getCurrentUser()
    {
        return Auth::user() ?? User::where('role', 'admin')->first();
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard()
    {
        $user = $this->getCurrentUser();
        if ($user && $user->isUser()) {
            return redirect()->route('tickets.myTickets')->with('error', 'สิทธิ์ผู้ใช้งานทั่วไปไม่สามารถเข้าถึงแดชบอร์ดแอดมินได้');
        }

        $tickets = Ticket::with(['technician', 'user'])->latest()->get();
        $technicians = User::where('role', 'technician')->withCount([
            'assignedTickets as active_tasks_count' => function ($query) {
                $query->whereIn('status', ['in_progress', 'waiting_parts']);
            },
            'assignedTickets as completed_tasks_count' => function ($query) {
                $query->where('status', 'completed');
            }
        ])->get();

        $stats = [
            'total' => $tickets->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'completed' => $tickets->where('status', 'completed')->count(),
            'unassigned' => $tickets->whereNull('technician_id')->where('status', '!=', 'completed')->count(),
        ];

        // Chart 1: Category Distribution
        $categories = [
            'คอมพิวเตอร์ / โน้ตบุ๊ก',
            'เครื่องพิมพ์ / สแกนเนอร์',
            'เครือข่าย / อินเทอร์เน็ต',
            'โปรแกรม / บัญชีผู้ใช้',
            'อุปกรณ์พ่วง / อื่นๆ',
        ];
        $categoryData = [];
        foreach ($categories as $cat) {
            $categoryData[] = $tickets->where('category', $cat)->count();
        }

        // Chart 2: Status Distribution
        $statusLabels = ['รอดำเนินการ', 'กำลังตรวจซ่อม', 'รออะไหล่', 'เสร็จสิ้นแล้ว'];
        $statusData = [
            $tickets->where('status', 'pending')->count(),
            $tickets->where('status', 'in_progress')->count(),
            $tickets->where('status', 'waiting_parts')->count(),
            $tickets->where('status', 'completed')->count(),
        ];

        // Chart 3: 4 Technicians Workload Comparison
        $techNames = $technicians->pluck('name')->map(fn($n) => explode(' ', $n)[0])->toArray();
        $techActive = $technicians->pluck('active_tasks_count')->toArray();
        $techCompleted = $technicians->pluck('completed_tasks_count')->toArray();

        return view('admin.dashboard', [
            'stats' => $stats,
            'tickets' => $tickets,
            'technicians' => $technicians,
            'categoryChart' => [
                'labels' => $categories,
                'data' => $categoryData,
            ],
            'statusChart' => [
                'labels' => $statusLabels,
                'data' => $statusData,
            ],
            'technicianChart' => [
                'labels' => $techNames,
                'active' => $techActive,
                'completed' => $techCompleted,
            ],
        ]);
    }

    /**
     * Technician Workspace (Dashboard)
     */
    public function technicianDashboard(Request $request)
    {
        $user = $this->getCurrentUser();
        if ($user && $user->isUser()) {
            return redirect()->route('tickets.myTickets')->with('error', 'เฉพาะช่างซ่อมไอทีเท่านั้นที่เข้าถึงหน้านี้ได้');
        }

        // If logged in as a specific technician, prioritize their tasks
        $techId = $user->isTechnician() ? $user->id : ($request->input('tech_id') ?? User::where('role', 'technician')->first()?->id);
        $currentTech = User::find($techId) ?? $user;

        // My assigned tickets
        $myTickets = Ticket::where('technician_id', $techId)->latest()->get();

        // Tickets pool ready to be claimed (pending or unassigned)
        $unassignedTickets = Ticket::whereNull('technician_id')
            ->whereIn('status', ['pending'])
            ->latest()
            ->get();

        $allTechnicians = User::where('role', 'technician')->get();

        return view('technician.dashboard', [
            'currentTech' => $currentTech,
            'myTickets' => $myTickets,
            'unassignedTickets' => $unassignedTickets,
            'allTechnicians' => $allTechnicians,
        ]);
    }

    /**
     * Technician Claims a Ticket (กดรับงานเอง)
     */
    public function claimTicket($id)
    {
        $user = $this->getCurrentUser();
        if ($user->isUser()) {
            return back()->with('error', 'คุณไม่มีสิทธิ์กดรับงานนี้');
        }

        $ticket = Ticket::findOrFail($id);

        if ($ticket->technician_id && $ticket->technician_id !== $user->id) {
            return back()->with('error', 'งานนี้มีช่างท่านอื่นรับไปแล้ว (' . $ticket->technician_name . ')');
        }

        $ticket->update([
            'technician_id' => $user->id,
            'technician_name' => $user->name,
            'status' => 'in_progress',
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'ช่างกดรับงานเอง (Claim Ticket)',
            'by_name' => $user->name,
            'note' => 'ช่าง ' . $user->name . ' ได้กดรับงานนี้เข้าสู่รายการงานที่ต้องทำเรียบร้อยแล้ว',
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'คุณได้กดรับงาน ' . $ticket->ticket_no . ' เรียบร้อยแล้ว!');
    }

    /**
     * Admin Assigns a Ticket to one of the 4 Technicians
     */
    public function assignTicket(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if (!$user->isAdmin()) {
            return back()->with('error', 'เฉพาะแอดมินเท่านั้นที่มีสิทธิ์มอบหมายงาน');
        }

        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $tech = User::findOrFail($request->technician_id);

        $ticket->update([
            'technician_id' => $tech->id,
            'technician_name' => $tech->name,
            'status' => ($ticket->status === 'pending') ? 'in_progress' : $ticket->status,
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'แอดมินมอบหมายงานให้ช่าง',
            'by_name' => $user->name,
            'note' => 'แอดมินได้มอบหมายงานนี้ให้แก่ ' . $tech->name . ' (' . ($tech->specialty ?? 'ช่างไอที') . ')',
        ]);

        return back()->with('success', 'มอบหมายงานให้ ' . $tech->name . ' เรียบร้อยแล้ว');
    }

    /**
     * Normal User - My Tickets History
     */
    public function myTickets()
    {
        $user = $this->getCurrentUser();

        $query = Ticket::query()->latest();
        if ($user && $user->isUser()) {
            $query->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('reporter_name', $user->name)
                  ->orWhere('phone', $user->phone);
            });
        }

        $tickets = $query->get();

        return view('tickets.my-tickets', [
            'tickets' => $tickets,
            'user' => $user,
        ]);
    }

    /**
     * All Tickets (Filtered by role)
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser();

        // If normal user clicks all tickets, redirect them to my tickets
        if ($user && $user->isUser()) {
            return redirect()->route('tickets.myTickets');
        }

        $query = Ticket::with(['technician', 'user'])->latest();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_no', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('reporter_name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by specific technician
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->input('technician_id'));
        }

        $tickets = $query->get();
        $technicians = User::where('role', 'technician')->get();

        return view('tickets.index', [
            'tickets' => $tickets,
            'technicians' => $technicians,
        ]);
    }

    /**
     * Create Ticket Form
     */
    public function create()
    {
        $user = $this->getCurrentUser();
        return view('tickets.create', ['user' => $user]);
    }

    /**
     * Store New Ticket
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:normal,high,urgent',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'reporter_name' => 'required|string|max:255',
            'department' => 'required|string',
            'phone' => 'required|string|max:50',
        ]);

        $user = Auth::user();
        $countToday = Ticket::whereDate('created_at', today())->count() + 1;
        $ticketNo = 'TK-' . date('Ym') . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $categoryNames = [
            'computer' => 'คอมพิวเตอร์ / โน้ตบุ๊ก',
            'printer' => 'เครื่องพิมพ์ / สแกนเนอร์',
            'network' => 'เครือข่าย / อินเทอร์เน็ต',
            'software' => 'โปรแกรม / บัญชีผู้ใช้',
            'other' => 'อุปกรณ์พ่วง / อื่นๆ',
        ];
        $categoryLabel = $categoryNames[$request->category] ?? $request->category;

        // Image uploads
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/tickets'), $filename);
                    $uploadedImages[] = asset('uploads/tickets/' . $filename);
                }
            }
        }

        $ticket = Ticket::create([
            'ticket_no' => $ticketNo,
            'title' => $request->title,
            'category' => $categoryLabel,
            'asset_code' => $request->asset_code,
            'location' => $request->location,
            'priority' => $request->priority,
            'description' => $request->description,
            'images' => $uploadedImages,
            'user_id' => $user?->id,
            'reporter_name' => $request->reporter_name,
            'department' => $request->department,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user?->id,
            'action' => 'แจ้งเรื่องเข้าระบบสำเร็จ',
            'by_name' => $request->reporter_name,
            'note' => 'ส่งคำขอแจ้งซ่อมผ่านระบบออนไลน์ รอมอบหมายช่างหรือช่างกดรับงาน',
        ]);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'ส่งใบแจ้งซ่อมสำเร็จ! หมายเลขงานคือ ' . $ticketNo);
    }

    /**
     * Show Ticket Details
     */
    public function show($id)
    {
        $ticket = Ticket::with(['logs', 'technician', 'user'])->findOrFail($id);
        $user = $this->getCurrentUser();
        $technicians = User::where('role', 'technician')->get();

        return view('tickets.show', [
            'ticket' => $ticket,
            'user' => $user,
            'technicians' => $technicians,
        ]);
    }

    /**
     * Update Ticket Status (For Technicians / Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        if ($user->isUser()) {
            return back()->with('error', 'ผู้ใช้งานทั่วไปไม่สามารถอัปเดตสถานะงานได้');
        }

        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,in_progress,waiting_parts,completed,cancelled',
            'note' => 'nullable|string',
        ]);

        $statusTitles = [
            'pending' => 'รอดำเนินการ',
            'in_progress' => 'กำลังดำเนินการซ่อม',
            'waiting_parts' => 'รออะไหล่ / ส่งซ่อมภายนอก',
            'completed' => 'ซ่อมเสร็จสิ้น / ปิดงาน',
            'cancelled' => 'ยกเลิกใบงาน',
        ];

        $oldStatus = $ticket->status;
        $newStatus = $request->input('status');

        $ticket->status = $newStatus;
        if ($request->filled('note')) {
            $ticket->repair_notes = $request->note;
        }
        $ticket->save();

        TicketLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'action' => 'เปลี่ยนสถานะเป็น: ' . ($statusTitles[$newStatus] ?? $newStatus),
            'by_name' => $user->name,
            'note' => $request->input('note', 'ไม่มีบันทึกเพิ่มเติม'),
        ]);

        return back()->with('success', 'อัปเดตสถานะงานเรียบร้อยแล้ว');
    }

    /**
     * Public Tracking
     */
    public function track(Request $request)
    {
        $resultTicket = null;

        if ($request->filled('query')) {
            $query = trim($request->input('query'));
            $resultTicket = Ticket::with(['logs', 'technician'])
                ->where('ticket_no', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->first();
        }

        return view('tickets.track', [
            'resultTicket' => $resultTicket,
        ]);
    }
}
