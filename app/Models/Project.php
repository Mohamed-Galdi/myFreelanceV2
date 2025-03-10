<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ProjectObserver;

#[ObservedBy(ProjectObserver::class)]
class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'logo',
        'title',
        'description',
        'tech_stack',
        'github_repo',
        'live_link',
        'work_count',
        'total_revenue'
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

    public function updateRevenue()
    {
        $this->total_revenue = $this->works()->where('payment_status', 'paid')->sum('price');
        $this->save();

        $this->client->updateRevenue();
    }

    public function updateWorkCount()
    {
        $this->work_count = $this->works()->count();
        $this->save();
    }
}
