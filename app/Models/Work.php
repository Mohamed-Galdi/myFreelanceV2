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
        'project_status',
        'payment_status',
        'payment_method'
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    protected static function booted()
    {
        static::saved(function ($work) {
            $work->project->updateRevenue();
            $work->project->updateWorkCount();
        });

        static::deleted(function ($work) {
            $work->project->updateRevenue();
            $work->project->updateWorkCount();
        });
    }
}
