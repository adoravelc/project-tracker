<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;
use App\Models\Project;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::firstOrCreate(
            ['email' => 'admin@energeek.id'],
            [
                'name' => 'Admin Energeek',
                'password' => Hash::make('admPa$$Energeek'),
                'is_admin' => true,
            ]
        );

        $categories = [
            'TODO',
            'IN PROGRESS',
            'TESTING',
            'DONE',
            'PENDING'
        ];

        $categoryMap = [];

        foreach ($categories as $category) {
            $categoryMap[$category] = Category::firstOrCreate(['name' => $category]);
        }

        $project = Project::updateOrCreate(
            [
                'name' => 'TaskTracker',
                'created_by' => $admin->id,
            ],
            [
                'description' => 'Task & Project Tracker application built with Laravel 12 API and Vue 3 TypeScript. Technical Assessment for Junior Fullstack Web Developer at Energeek 2026. Deadline: 5 Maret 2026.',
                'status' => 'active',
            ]
        );

        $dueDate = '2026-03-05';

        $tasks = [
            [
                'title' => 'Create Backend (Laravel 12)',
                'description' => 'Build backend API endpoints and business logic using Laravel 12.',
                'category' => 'DONE',
            ],
            [
                'title' => 'Set up Database (PostgreSQL)',
                'description' => 'Configure PostgreSQL schema, relations, and seed baseline data.',
                'category' => 'DONE',
            ],
            [
                'title' => 'Create Frontend (Vue.js 3 + TypeScript)',
                'description' => 'Develop frontend interface with Vue 3 and TypeScript integration.',
                'category' => 'IN PROGRESS',
            ],
            [
                'title' => 'Create Documentation (API, README)',
                'description' => 'Prepare API documentation and project README.',
                'category' => 'TODO',
            ],
        ];

        foreach ($tasks as $task) {
            Task::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'title' => $task['title'],
                ],
                [
                    'description' => $task['description'],
                    'category_id' => $categoryMap[$task['category']]->id,
                    'created_by' => $admin->id,
                    'due_date' => $dueDate,
                ]
            );
        }
    }
}
