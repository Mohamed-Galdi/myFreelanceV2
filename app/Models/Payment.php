<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{

    protected $fillable = [
        'work_id',
        'amount',
        'payment_date',
        'payment_method',
        'note'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
