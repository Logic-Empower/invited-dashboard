<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Guest;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'capacity',
        'event_date',
        'expires_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'expires_at' => 'datetime',
    ];    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('event_date', '>', now())
                    ->where('expires_at', '>', now());
    }   
    
    /**
     * Get the number of seats already taken or reserved.
     */
    public function getOccupiedSeatsAttribute(): int
    {
        return (int) $this->guests()
            ->whereIn('status', ['confirmed', 'pending'])
            ->selectRaw("SUM(CASE 
                WHEN status = 'confirmed' THEN confirmed_seats 
                WHEN status = 'pending' THEN max_seats 
                ELSE 0 
            END) as total")
            ->value('total') ?? 0;
    }

    /**
     * Get the remaining available seats.
     */
    public function getRemainingSeatsAttribute(): ?int
    {
        if (is_null($this->capacity)) {
            return null; // Represents unlimited
        }

        return max(0, $this->capacity - $this->occupied_seats);
    }    

}