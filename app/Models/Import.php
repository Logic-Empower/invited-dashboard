<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'total_rows',
        'imported_rows',
        'status',
        'error',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'imported_rows' => 'integer',
    ];    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}