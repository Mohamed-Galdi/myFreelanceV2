<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ClientObserver;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact', 'source'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getTotalProjects()
    {
        return $this->projects()->count();
    }

    public function getTotalWorks()
    {
        return Work::whereHas('project', function ($query) {
            $query->where('client_id', $this->id);
        })->count();
    }

    public function getTotalPayments()
    {
        return Payment::whereHas('work.project', function ($query) {
            $query->where('client_id', $this->id);
        })->count();
    }

    public function getTotalRevenue()
    {
        return Payment::whereHas('work.project', function ($query) {
            $query->where('client_id', $this->id);
        })->sum('amount');
    }

    public function getPendingAmounts()
    {
        // Get total value of all works for this client
        $totalWorkValue = Work::whereHas('project', function ($query) {
            $query->where('client_id', $this->id);
        })->sum('price');

        // Get total payments received
        $totalPayments = Payment::whereHas('work.project', function ($query) {
            $query->where('client_id', $this->id);
        })->sum('amount');

        // Return the difference
        return $totalWorkValue - $totalPayments;
    }
}
