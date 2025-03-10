<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'TechSolutions Inc.',
                'contact' => 'contact@techsolutions.com',
                'source' => 'Upwork',
                'projects_count' => 0,
                'total_revenue' => 0
            ],
            [
                'name' => 'Creative Designs',
                'contact' => 'info@creativedesigns.com',
                'source' => 'Referral',
                'projects_count' => 0,
                'total_revenue' => 0
            ],
            [
                'name' => 'Global Commerce',
                'contact' => 'business@globalcommerce.com',
                'source' => 'LinkedIn',
                'projects_count' => 0,
                'total_revenue' => 0
            ],
            [
                'name' => 'Marketing Experts',
                'contact' => 'hello@marketingexperts.com',
                'source' => 'Fiverr',
                'projects_count' => 0,
                'total_revenue' => 0
            ],
            [
                'name' => 'Startup Innovations',
                'contact' => 'team@startupinnovations.com',
                'source' => 'Conference',
                'projects_count' => 0,
                'total_revenue' => 0
            ]
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
