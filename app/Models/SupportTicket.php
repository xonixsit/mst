<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class SupportTicket extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'ticket_number',
        'client_id',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastTicket ? (int) substr($lastTicket->ticket_number, -5) + 1 : 1;
        
        return 'TKT-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    public function addReply(User $user, string $message, ?string $attachmentPath = null): TicketReply
    {
        return $this->replies()->create([
            'user_id' => $user->id,
            'message' => $message,
            'attachment_path' => $attachmentPath,
        ]);
    }

    public function resolve(User $resolvedBy): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'assigned_to' => $resolvedBy->id,
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function assign(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
        ]);
    }

    public function updatePriority(string $priority): void
    {
        $this->update(['priority' => $priority]);
    }
}
