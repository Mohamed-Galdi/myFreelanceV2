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
        return $this->projects()->works()->count();
    }

    public function getTotalPayments()
    {
        return $this->projects()->works()->payments()->count();
    }

    public function getTotalRevenue()
    {
        return $this->projects()->works()->payments()->sum('amount');
    }

    // public function getPendingAmounts(){
    //     // TODO
    // }

}
