<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_no',
        'title',
        'category',
        'asset_code',
        'location',
        'priority',
        'description',
        'images',
        'user_id',
        'reporter_name',
        'department',
        'phone',
        'technician_id',
        'technician_name',
        'status',
        'repair_notes',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class, 'ticket_id')->latest();
    }
}
