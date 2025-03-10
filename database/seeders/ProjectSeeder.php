<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Client;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $techSolutions = Client::where('name', 'TechSolutions Inc.')->first();
        $creativeDesigns = Client::where('name', 'Creative Designs')->first();
        $globalCommerce = Client::where('name', 'Global Commerce')->first();
        $marketingExperts = Client::where('name', 'Marketing Experts')->first();
        $startupInnovations = Client::where('name', 'Startup Innovations')->first();

        $projects = [
            [
                'client_id' => $techSolutions->id,
                'logo' => null,
                'title' => 'E-commerce Platform',
                'description' => 'Developed a full-featured e-commerce platform with product management, cart functionality, and payment integration.',
                'tech_stack' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe API'],
                'github_repo' => 'https://github.com/demo/ecommerce-platform',
                'live_link' => 'https://ecommerce-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $techSolutions->id,
                'logo' => null,
                'title' => 'Admin Dashboard',
                'description' => 'Built an admin dashboard for managing users, products, and orders.',
                'tech_stack' => ['Laravel', 'Inertia', 'Vue.js', 'MySQL'],
                'github_repo' => 'https://github.com/demo/admin-dashboard',
                'live_link' => 'https://admin-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $creativeDesigns->id,
                'logo' => null,
                'title' => 'Portfolio Website',
                'description' => 'Designed and developed a responsive portfolio website showcasing client work.',
                'tech_stack' => ['Vue.js', 'Tailwind CSS', 'Netlify'],
                'github_repo' => 'https://github.com/demo/portfolio-site',
                'live_link' => 'https://portfolio-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $globalCommerce->id,
                'logo' => null,
                'title' => 'Inventory Management System',
                'description' => 'Created a system for tracking inventory, managing suppliers, and generating reports.',
                'tech_stack' => ['Laravel', 'MySQL', 'Tailwind CSS', 'Alpine.js'],
                'github_repo' => 'https://github.com/demo/inventory-system',
                'live_link' => 'https://inventory-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $globalCommerce->id,
                'logo' => null,
                'title' => 'Order Tracking App',
                'description' => 'Developed a mobile-friendly application for tracking orders and deliveries.',
                'tech_stack' => ['Laravel', 'Vue.js', 'MySQL', 'Google Maps API'],
                'github_repo' => 'https://github.com/demo/order-tracker',
                'live_link' => 'https://order-tracker-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $marketingExperts->id,
                'logo' => null,
                'title' => 'Email Campaign Manager',
                'description' => 'Built a tool for creating, sending, and analyzing email marketing campaigns.',
                'tech_stack' => ['Laravel', 'Vue.js', 'MySQL', 'Mailchimp API'],
                'github_repo' => 'https://github.com/demo/email-campaigns',
                'live_link' => 'https://email-campaign-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $startupInnovations->id,
                'logo' => null,
                'title' => 'Mobile App Landing Page',
                'description' => 'Designed and developed a landing page for a mobile app launch.',
                'tech_stack' => ['Vue.js', 'Tailwind CSS', 'Netlify'],
                'github_repo' => 'https://github.com/demo/app-landing',
                'live_link' => 'https://app-landing-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ],
            [
                'client_id' => $startupInnovations->id,
                'logo' => null,
                'title' => 'User Authentication System',
                'description' => 'Implemented a secure user authentication and authorization system.',
                'tech_stack' => ['Laravel', 'Laravel Sanctum', 'MySQL'],
                'github_repo' => 'https://github.com/demo/auth-system',
                'live_link' => 'https://auth-demo.com',
                'work_count' => 0,
                'total_revenue' => 0
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}
