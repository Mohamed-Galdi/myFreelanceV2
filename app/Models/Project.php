<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ProjectObserver;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'logo',
        'image',
        'tech_stack',
        'github_repo',
        'live_link',
    ];

    protected $casts = [
        'tech_stack' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function works()
    {
        return $this->hasMany(Work::class);
    }

    public function getTotalWorks()
    {
        return $this->works()->count();
    }

    public function getTotalPayments()
    {
        return Payment::whereHas('work', function ($query) {
            $query->where('project_id', $this->id);
        })->sum('amount');
    }

    public function getTotalRevenue()
    {
        return Payment::whereHas('work', function ($query) {
            $query->where('project_id', $this->id);
        })->sum('amount');
    }

}
