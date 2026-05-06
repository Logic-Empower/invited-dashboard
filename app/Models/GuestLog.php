<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuestLog extends Model
{
    protected $fillable = [
        'guest_id',
        'action',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}