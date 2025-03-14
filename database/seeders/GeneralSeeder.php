<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clients seeder
        $clients = [
            [
                'id' => 1,
                'name' => 'Yazzid Altaouil',
                'contact' => '+996',
                'source' => 'Upwork',
                'created_at' => '2025-03-14 00:42:41',
                'updated_at' => '2025-03-14 00:42:41',
            ],
            [
                'id' => 2,
                'name' => 'Mohamed Elghamedi',
                'contact' => '+996',
                'source' => 'Upwork',
                'created_at' => '2025-03-14 00:43:50',
                'updated_at' => '2025-03-14 00:43:50',
            ],
            [
                'id' => 3,
                'name' => 'Khalid aldoussi',
                'contact' => '+996',
                'source' => 'Baaeed',
                'created_at' => '2025-03-14 00:44:18',
                'updated_at' => '2025-03-14 00:44:18',
            ],
            [
                'id' => 4,
                'name' => 'Fawziya',
                'contact' => '+996',
                'source' => 'Recommendation',
                'created_at' => '2025-03-14 00:44:41',
                'updated_at' => '2025-03-14 00:44:41',
            ],
            [
                'id' => 6,
                'name' => 'X girl',
                'contact' => '+996',
                'source' => 'Recommendation',
                'created_at' => '2025-03-14 00:46:00',
                'updated_at' => '2025-03-14 00:46:42',
            ],
            [
                'id' => 7,
                'name' => 'Sifedine (Mich)',
                'contact' => '+996',
                'source' => 'Upwork',
                'created_at' => '2025-03-14 00:47:11',
                'updated_at' => '2025-03-14 00:47:11',
            ],
        ];

        DB::table('clients')->insert($clients);

        // Projects seeder
        $projects = [
            [
                'id' => 1,
                'client_id' => 1,
                'title' => 'Ethaf',
                'description' => 'Quran',
                'logo' => 'storage/projects/logos//ethaf-1741913384-VJqGx3YJ.png',
                'image' => 'storage/projects/images/1/ethaf-1741913384-TdmwnF39.png',
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:49:44',
                'updated_at' => '2025-03-14 00:49:44',
            ],
            [
                'id' => 2,
                'client_id' => 2,
                'title' => 'Sahim',
                'description' => 'SurPlus food platform',
                'logo' => 'storage/projects/logos//sahim-1741913558-XuMhu1ST.png',
                'image' => 'storage/projects/images/2/sahim-1741913558-RRkYpdmj.png',
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:52:38',
                'updated_at' => '2025-03-14 00:52:38',
            ],
            [
                'id' => 3,
                'client_id' => 3,
                'title' => 'Wassilni',
                'description' => 'trans app',
                'logo' => null,
                'image' => null,
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:53:24',
                'updated_at' => '2025-03-14 00:53:24',
            ],
            [
                'id' => 4,
                'client_id' => 2,
                'title' => 'Tawsila',
                'description' => 'School bus app',
                'logo' => 'storage/projects/logos//tawsila-1741913690-Reqz35yh.png',
                'image' => 'storage/projects/images/4/tawsila-1741913690-W82LVABt.png',
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:54:50',
                'updated_at' => '2025-03-14 00:54:50',
            ],
            [
                'id' => 5,
                'client_id' => 4,
                'title' => 'Saudi brands',
                'description' => 'Platform o support Saudi brands',
                'logo' => null,
                'image' => null,
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:55:41',
                'updated_at' => '2025-03-14 00:55:41',
            ],
            [
                'id' => 6,
                'client_id' => 2,
                'title' => 'Yarub',
                'description' => 'Learn Arabic platform',
                'logo' => 'storage/projects/logos//yarub-1741913840-II6SSKmz.png',
                'image' => 'storage/projects/images/6/yarub-1741913840-vohBKJSm.png',
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:57:20',
                'updated_at' => '2025-03-14 00:57:20',
            ],
            [
                'id' => 7,
                'client_id' => 6,
                'title' => 'MybookToYou',
                'description' => 'Sharing books platform',
                'logo' => null,
                'image' => null,
                'tech_stack' => '["php"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 00:58:34',
                'updated_at' => '2025-03-14 00:58:34',
            ],
            [
                'id' => 8,
                'client_id' => 2,
                'title' => 'Awab',
                'description' => 'Yarub Clone',
                'logo' => 'storage/projects/logos//awab-1741914014-SZuTRjcN.png',
                'image' => 'storage/projects/images/8/awab-1741914014-ufXqPNnM.png',
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 01:00:14',
                'updated_at' => '2025-03-14 01:00:15',
            ],
            [
                'id' => 9,
                'client_id' => 7,
                'title' => 'Qurmed',
                'description' => 'Medcine Platform',
                'logo' => null,
                'image' => null,
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 01:00:56',
                'updated_at' => '2025-03-14 01:00:56',
            ],
            [
                'id' => 10,
                'client_id' => 2,
                'title' => 'Massakin',
                'description' => 'Mobile Platform',
                'logo' => null,
                'image' => null,
                'tech_stack' => '["laravel"]',
                'github_repo' => null,
                'live_link' => null,
                'created_at' => '2025-03-14 01:01:27',
                'updated_at' => '2025-03-14 01:01:27',
            ],
        ];

        DB::table('projects')->insert($projects);

        // Works seeder
        $works = [
            [
                'id' => 1,
                'project_id' => 1,
                'description' => 'Work as an employee',
                'price' => '800.00',
                'start_date' => '2023-10-24',
                'end_date' => '2023-11-29',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:04:16',
                'updated_at' => '2025-03-14 01:05:48',
            ],
            [
                'id' => 2,
                'project_id' => 2,
                'description' => 'Build the project',
                'price' => '350.00',
                'start_date' => '2023-12-11',
                'end_date' => '2023-12-30',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:10:43',
                'updated_at' => '2025-03-14 01:12:14',
            ],
            [
                'id' => 3,
                'project_id' => 2,
                'description' => 'Project Updates',
                'price' => '150.00',
                'start_date' => '2024-01-06',
                'end_date' => '2024-01-12',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:12:06',
                'updated_at' => '2025-03-14 01:12:24',
            ],
            [
                'id' => 4,
                'project_id' => 3,
                'description' => 'Work as an employee',
                'price' => '200.00',
                'start_date' => '2024-02-05',
                'end_date' => '2024-02-28',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:13:29',
                'updated_at' => '2025-03-14 01:13:41',
            ],
            [
                'id' => 5,
                'project_id' => 4,
                'description' => 'Build the project',
                'price' => '550.00',
                'start_date' => '2024-03-16',
                'end_date' => '2024-04-15',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:14:33',
                'updated_at' => '2025-03-14 01:15:39',
            ],
            [
                'id' => 6,
                'project_id' => 5,
                'description' => 'Build the project in Laravel',
                'price' => '300.00',
                'start_date' => '2024-04-25',
                'end_date' => '2024-04-28',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:15:31',
                'updated_at' => '2025-03-14 01:15:45',
            ],
            [
                'id' => 7,
                'project_id' => 4,
                'description' => 'Install the project on 2 devices',
                'price' => '80.00',
                'start_date' => '2024-05-10',
                'end_date' => '2024-05-10',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:16:37',
                'updated_at' => '2025-03-14 01:18:31',
            ],
            [
                'id' => 8,
                'project_id' => 6,
                'description' => 'Build the project',
                'price' => '900.00',
                'start_date' => '2024-06-29',
                'end_date' => '2024-08-27',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:19:44',
                'updated_at' => '2025-03-14 01:19:53',
            ],
            [
                'id' => 9,
                'project_id' => 6,
                'description' => 'Project Updates',
                'price' => '180.00',
                'start_date' => '2024-08-28',
                'end_date' => '2024-09-02',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:20:39',
                'updated_at' => '2025-03-14 01:20:46',
            ],
            [
                'id' => 10,
                'project_id' => 2,
                'description' => 'Make it live for 2 girls from the team',
                'price' => '30.00',
                'start_date' => '2024-09-26',
                'end_date' => '2024-09-26',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:22:05',
                'updated_at' => '2025-03-14 01:58:42',
            ],
            [
                'id' => 11,
                'project_id' => 7,
                'description' => 'CRUDs in PHP',
                'price' => '120.00',
                'start_date' => '2024-10-28',
                'end_date' => '2024-10-29',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:23:20',
                'updated_at' => '2025-03-14 01:23:40',
            ],
            [
                'id' => 12,
                'project_id' => 8,
                'description' => 'Clone Yarub',
                'price' => '30.00',
                'start_date' => '2024-12-24',
                'end_date' => '2024-12-25',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:24:48',
                'updated_at' => '2025-03-14 01:24:55',
            ],
            [
                'id' => 13,
                'project_id' => 9,
                'description' => 'Build the project',
                'price' => '450.00',
                'start_date' => '2025-01-26',
                'end_date' => '2025-02-25',
                'status' => 'Ongoing',
                'created_at' => '2025-03-14 01:26:01',
                'updated_at' => '2025-03-14 01:26:01',
            ],
            [
                'id' => 14,
                'project_id' => 8,
                'description' => 'Fix Bugs (switch videos storage from S3 to server)',
                'price' => '80.00',
                'start_date' => '2025-02-27',
                'end_date' => '2025-03-01',
                'status' => 'Completed',
                'created_at' => '2025-03-14 01:27:29',
                'updated_at' => '2025-03-14 01:27:37',
            ],
            [
                'id' => 15,
                'project_id' => 10,
                'description' => 'Build the project',
                'price' => '1300.00',
                'start_date' => '2025-03-12',
                'end_date' => null,
                'status' => 'Ongoing',
                'created_at' => '2025-03-14 01:28:20',
                'updated_at' => '2025-03-14 01:28:20',
            ],
        ];

        DB::table('works')->insert($works);

        // Payments seeder
        $payments = [
            [
                'id' => 1,
                'work_id' => 1,
                'amount' => '100.00',
                'payment_date' => '2023-11-07',
                'payment_method' => 'Upwork',
                'note' => 'payment of last week of October 2023',
                'created_at' => '2025-03-14 01:36:25',
                'updated_at' => '2025-03-14 01:40:50',
            ],
            [
                'id' => 2,
                'work_id' => 1,
                'amount' => '700.00',
                'payment_date' => '2023-12-05',
                'payment_method' => 'Upwork',
                'note' => 'payment for November 2023',
                'created_at' => '2025-03-14 01:37:46',
                'updated_at' => '2025-03-14 01:37:46',
            ],
            [
                'id' => 3,
                'work_id' => 2,
                'amount' => '350.00',
                'payment_date' => '2024-01-05',
                'payment_method' => 'Upwork',
                'note' => null,
                'created_at' => '2025-03-14 01:38:40',
                'updated_at' => '2025-03-14 01:38:40',
            ],
            [
                'id' => 4,
                'work_id' => 3,
                'amount' => '150.00',
                'payment_date' => '2024-01-06',
                'payment_method' => 'PayPal',
                'note' => null,
                'created_at' => '2025-03-14 01:41:37',
                'updated_at' => '2025-03-14 01:41:37',
            ],
            [
                'id' => 5,
                'work_id' => 4,
                'amount' => '200.00',
                'payment_date' => '2024-02-27',
                'payment_method' => 'Western Union',
                'note' => null,
                'created_at' => '2025-03-14 01:42:21',
                'updated_at' => '2025-03-14 01:42:21',
            ],
            [
                'id' => 6,
                'work_id' => 5,
                'amount' => '550.00',
                'payment_date' => '2024-04-19',
                'payment_method' => 'PayPal',
                'note' => 'He acctually paid me 600$, may be because he was overdue',
                'created_at' => '2025-03-14 01:44:18',
                'updated_at' => '2025-03-14 01:44:18',
            ],
            [
                'id' => 7,
                'work_id' => 6,
                'amount' => '150.00',
                'payment_date' => '2024-04-25',
                'payment_method' => 'Western Union',
                'note' => 'Part 1',
                'created_at' => '2025-03-14 01:46:36',
                'updated_at' => '2025-03-14 01:46:36',
            ],
            [
                'id' => 8,
                'work_id' => 6,
                'amount' => '150.00',
                'payment_date' => '2024-04-28',
                'payment_method' => 'Western Union',
                'note' => 'Part 2',
                'created_at' => '2025-03-14 01:47:33',
                'updated_at' => '2025-03-14 01:47:33',
            ],
            [
                'id' => 9,
                'work_id' => 7,
                'amount' => '80.00',
                'payment_date' => '2024-05-24',
                'payment_method' => 'Western Union',
                'note' => null,
                'created_at' => '2025-03-14 01:48:15',
                'updated_at' => '2025-03-14 01:48:15',
            ],
            [
                'id' => 10,
                'work_id' => 8,
                'amount' => '450.00',
                'payment_date' => '2024-06-29',
                'payment_method' => 'Western Union',
                'note' => '4460 in MAD',
                'created_at' => '2025-03-14 01:49:13',
                'updated_at' => '2025-03-14 01:49:13',
            ],
            [
                'id' => 11,
                'work_id' => 8,
                'amount' => '450.00',
                'payment_date' => '2024-08-27',
                'payment_method' => 'Western Union',
                'note' => '1800 in SAR, 4500 in MAD',
                'created_at' => '2025-03-14 01:50:33',
                'updated_at' => '2025-03-14 01:52:59',
            ],
            [
                'id' => 12,
                'work_id' => 9,
                'amount' => '180.00',
                'payment_date' => '2024-09-10',
                'payment_method' => 'Western Union',
                'note' => '1750 in MAD',
                'created_at' => '2025-03-14 01:51:29',
                'updated_at' => '2025-03-14 01:51:29',
            ],
            [
                'id' => 13,
                'work_id' => 10,
                'amount' => '30.00',
                'payment_date' => '2024-09-26',
                'payment_method' => 'Western Union',
                'note' => null,
                'created_at' => '2025-03-14 01:53:56',
                'updated_at' => '2025-03-14 01:53:56',
            ],
            [
                'id' => 14,
                'work_id' => 11,
                'amount' => '120.00',
                'payment_date' => '2024-10-28',
                'payment_method' => 'Western Union',
                'note' => null,
                'created_at' => '2025-03-14 01:55:06',
                'updated_at' => '2025-03-14 01:55:06',
            ],
            [
                'id' => 15,
                'work_id' => 12,
                'amount' => '30.00',
                'payment_date' => '2024-12-24',
                'payment_method' => 'Western Union',
                'note' => null,
                'created_at' => '2025-03-14 01:56:16',
                'updated_at' => '2025-03-14 01:56:16',
            ],
            [
                'id' => 16,
                'work_id' => 13,
                'amount' => '50.00',
                'payment_date' => '2025-01-27',
                'payment_method' => 'Bank Transfer',
                'note' => 'Avance',
                'created_at' => '2025-03-14 01:56:58',
                'updated_at' => '2025-03-14 01:56:58',
            ],
            [
                'id' => 17,
                'work_id' => 13,
                'amount' => '150.00',
                'payment_date' => '2025-02-12',
                'payment_method' => 'Bank Transfer',
                'note' => 'Part 2',
                'created_at' => '2025-03-14 01:57:29',
                'updated_at' => '2025-03-14 01:57:29',
            ],
            [
                'id' => 18,
                'work_id' => 14,
                'amount' => '80.00',
                'payment_date' => '2025-02-27',
                'payment_method' => 'Western Union',
                'note' => 'Part 2',
                'created_at' => '2025-03-14 01:57:29',
                'updated_at' => '2025-03-14 01:57:29',
            ],
            [
                'id' => 19,
                'work_id' => 15,
                'amount' => '436.00',
                'payment_date' => '2025-03-12',
                'payment_method' => 'Western Union',
                'note' => 'First 1/3',
                'created_at' => '2025-03-14 01:58:14',
                'updated_at' => '2025-03-14 01:58:14',
            ],
        ];

        DB::table('payments')->insert($payments);
    }
}
