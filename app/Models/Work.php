<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'description',
        'price',
        'start_date',
        'end_date',
        'status'
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPayments()
    {
        return $this->payments()->count();
    }

    public function getReceivedAmount()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingAmount()
    {
        return $this->price - $this->getReceivedAmount();
    }

    public function getPaymentPercentage()
    {
        return $this->getReceivedAmount() / $this->price * 100;
    }

}
