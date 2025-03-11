<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ClientObserver;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'contact', 'source', 'projects_count', 'total_revenue'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function updateRevenue()
    {
        $this->total_revenue = $this->projects()->sum('total_revenue');
        $this->save();
    }

    public function updateProjectCount()
    {
        $this->projects_count = $this->projects()->count();
        $this->save();
    }
}
