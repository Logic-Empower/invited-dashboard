<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Event;

class Guest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'mobile',
        'email',
        'max_seats',
        'confirmed_seats',
        'status',
        'invite_code',
        'viewed_at',
        'responded_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function logs()
    {
        return $this->hasMany(GuestLog::class);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }    
}