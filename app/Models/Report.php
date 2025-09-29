<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reporter_type',
        'reported_id',
        'reported_type',
        'reason',
        'description',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    const STATUSES = [
        'pending' => 'Pending Review',
        'reviewed' => 'Under Review',
        'resolved' => 'Resolved',
        'dismissed' => 'Dismissed',
    ];

    /**
     * Get the reporter (user who made the report)
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the reported user
     */
    public function reported(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    /**
     * Check if report is from vendor
     */
    public function isFromVendor(): bool
    {
        return $this->reporter_type === 'vendor';
    }

    /**
     * Check if report is from client
     */
    public function isFromClient(): bool
    {
        return $this->reporter_type === 'client';
    }

    /**
     * Check if report is about a vendor
     */
    public function isAboutVendor(): bool
    {
        return $this->reported_type === 'vendor';
    }

    /**
     * Check if report is about a client
     */
    public function isAboutClient(): bool
    {
        return $this->reported_type === 'client';
    }

    /**
     * Get human readable status
     */
    public function getStatusTextAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get reporter type label
     */
    public function getReporterTypeLabelAttribute(): string
    {
        return $this->reporter_type === 'vendor' ? 'Vendor' : 'Client';
    }

    /**
     * Get reported type label
     */
    public function getReportedTypeLabelAttribute(): string
    {
        return $this->reported_type === 'vendor' ? 'Vendor' : 'Client';
    }

    /**
     * Scope for vendor reports
     */
    public function scopeVendorReports($query)
    {
        return $query->where('reported_type', 'vendor');
    }

    /**
     * Scope for client reports
     */
    public function scopeClientReports($query)
    {
        return $query->where('reported_type', 'client');
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
