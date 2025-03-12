<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Work;
use App\Models\Project;
use Carbon\Carbon;

class WorkSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        $works = [
            // E-commerce Platform
            [
                'project_id' => $projects->where('title', 'E-commerce Platform')->first()->id,
                'description' => 'Initial project setup and database design',
                'price' => 500.00,
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(55),
                'payment_status' => 'paid',
            ],
            [
                'project_id' => $projects->where('title', 'E-commerce Platform')->first()->id,
                'description' => 'Product management implementation',
                'price' => 800.00,
                'start_date' => Carbon::now()->subDays(50),
                'end_date' => Carbon::now()->subDays(45),
                'payment_status' => 'paid',
            ],
            [
                'project_id' => $projects->where('title', 'E-commerce Platform')->first()->id,
                'description' => 'Shopping cart functionality',
                'price' => 600.00,
                'start_date' => Carbon::now()->subDays(40),
                'end_date' => Carbon::now()->subDays(35),
                'payment_status' => 'pending',
            ],

            // Admin Dashboard
            [
                'project_id' => $projects->where('title', 'Admin Dashboard')->first()->id,
                'description' => 'Dashboard layout and components',
                'price' => 400.00,
                'start_date' => Carbon::now()->subDays(30),
                'end_date' => Carbon::now()->subDays(25),
                'payment_status' => 'paid',
            ],

            // Portfolio Website
            [
                'project_id' => $projects->where('title', 'Portfolio Website')->first()->id,
                'description' => 'Design mockups and wireframes',
                'price' => 300.00,
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->subDays(18),
                'payment_status' => 'paid',
            ],
            [
                'project_id' => $projects->where('title', 'Portfolio Website')->first()->id,
                'description' => 'Frontend implementation',
                'price' => 500.00,
                'start_date' => Carbon::now()->subDays(17),
                'end_date' => Carbon::now()->subDays(12),
                'payment_status' => 'paid',
            ],

            // Inventory Management System
            [
                'project_id' => $projects->where('title', 'Inventory Management System')->first()->id,
                'description' => 'Database design and initial setup',
                'price' => 600.00,
                'start_date' => Carbon::now()->subDays(45),
                'end_date' => Carbon::now()->subDays(40),
                'payment_status' => 'paid',
            ],
            [
                'project_id' => $projects->where('title', 'Inventory Management System')->first()->id,
                'description' => 'Supplier management module',
                'price' => 500.00,
                'start_date' => Carbon::now()->subDays(35),
                'end_date' => Carbon::now()->subDays(30),
                'payment_status' => 'pending',
            ],

            // Order Tracking App
            [
                'project_id' => $projects->where('title', 'Order Tracking App')->first()->id,
                'description' => 'Google Maps integration',
                'price' => 400.00,
                'start_date' => Carbon::now()->subDays(25),
                'end_date' => Carbon::now()->subDays(20),
                'payment_status' => 'paid',
            ],

            // Email Campaign Manager
            [
                'project_id' => $projects->where('title', 'Email Campaign Manager')->first()->id,
                'description' => 'Email template builder',
                'price' => 700.00,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->subDays(10),
                'payment_status' => 'paid',
            ],

            // Mobile App Landing Page
            [
                'project_id' => $projects->where('title', 'Mobile App Landing Page')->first()->id,
                'description' => 'Responsive design implementation',
                'price' => 350.00,
                'start_date' => Carbon::now()->subDays(12),
                'end_date' => Carbon::now()->subDays(8),
                'payment_status' => 'paid',
            ],

            // User Authentication System
            [
                'project_id' => $projects->where('title', 'User Authentication System')->first()->id,
                'description' => 'Implement Laravel Sanctum',
                'price' => 450.00,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->subDays(5),
                'payment_status' => 'pending',
            ],
        ];

        foreach ($works as $work) {
            Work::create($work);
        }
    }
}
