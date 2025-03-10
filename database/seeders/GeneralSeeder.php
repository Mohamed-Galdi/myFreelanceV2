<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class GeneralSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create 5 clients
        $clients = [];
        for ($i = 1; $i <= 5; $i++) {
            $clients[] = Client::create([
                'name' => $faker->company,
                'contact' => $faker->email,
                'source' => $faker->randomElement(['referral', 'website', 'social_media', 'other']),
                'projects_count' => 0, // Will be updated later
                'total_revenue' => 0,  // Will be updated later
            ]);
        }

        // Create 8 projects across the clients
        $projects = [];
        foreach ($clients as $client) {
            $projectCount = $faker->numberBetween(1, 3); // Each client gets 1-3 projects
            for ($j = 1; $j <= $projectCount; $j++) {
                $projects[] = Project::create([
                    'client_id' => $client->id,
                    'logo' => $faker->imageUrl(),
                    'title' => $faker->sentence(3),
                    'description' => $faker->paragraph,
                    'tech_stack' => json_encode($faker->randomElements(['Laravel', 'Vue.js', 'MySQL', 'Tailwind', 'React', 'PHP'], 3)),
                    'github_repo' => $faker->url,
                    'live_link' => $faker->url,
                    'work_count' => 0, // Will be updated later
                    'total_revenue' => 0, // Will be updated later
                ]);
            }
        }

        // Create 14 works across the projects
        for ($k = 1; $k <= 14; $k++) {
            $project = $faker->randomElement($projects);
            $startDate = $faker->dateTimeBetween('-18 months', 'now');
            $endDate = $faker->randomElement([null, $faker->dateTimeBetween($startDate, 'now')]);

            Work::create([
                'project_id' => $project->id,
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 50, 2000),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'project_status' => $faker->randomElement(['ongoing', 'completed', 'cancelled']),
                'payment_status' => $faker->randomElement(['paid', 'pending', 'refunded', 'cancelled']),
                'payment_method' => $faker->randomElement(['paypal', 'bank_transfer', 'platform', 'other']),
            ]);
        }

        // Update counts and revenue for all projects and clients
        foreach ($projects as $project) {
            $project->updateWorkCount();
            $project->updateRevenue();
        }

        foreach ($clients as $client) {
            $client->updateProjectCount();
            $client->updateRevenue();
        }
    }
}
