<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Designer;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Admin accounts -------------------------------------------------
        if (User::count() === 0) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@studiovolume.com',
                'password' => 'admin12345',
                'role' => 'super_admin',
                'is_active' => true,
            ]);

            User::create([
                'name' => 'Editor',
                'email' => 'editor@studiovolume.com',
                'password' => 'editor12345',
                'role' => 'editor',
                'is_active' => true,
            ]);
        }

        // ---- Categories -----------------------------------------------------
        $categoryIds = [];
        foreach (['Residential Architecture', 'Interior Architecture', 'Cultural Architecture', 'Hospitality Architecture', 'Master Planning'] as $i => $name) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'display_order' => $i + 1, 'is_published' => true]
            );
            $categoryIds[$name] = $category->id;
        }

        // ---- Projects (from the original static content) --------------------
        $i = 0;
        foreach (\App\Support\Projects::all() as $data) {
            $i++;
            $concept = $data['concept'];
            $conceptHtml = '<p>'.implode('</p><p>', $concept).'</p>';

            $project = Project::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'category_id' => $categoryIds[$data['category']] ?? null,
                    'location' => $data['location'],
                    'year' => $data['year'],
                    'area' => $data['area'],
                    'client' => $data['client'],
                    'type' => $data['type'],
                    'status' => $data['status'],
                    'excerpt' => $data['excerpt'],
                    'overview' => '',
                    'concept' => $conceptHtml,
                    'design_approach' => '',
                    'materials' => implode("\n", $data['materials']),
                    'challenges' => '',
                    'solution' => '',
                    'is_published' => true,
                    'display_order' => $i,
                    'seo_title' => $data['name'].' — '.$data['category'].' in '.$data['location'],
                    'seo_description' => $data['excerpt'],
                ]
            );

            // Homepage featured order follows the display order of the first four.
            if ($i <= 4) {
                $project->update(['is_featured' => true, 'featured_order' => $i]);
            }
        }

        // ---- Services -------------------------------------------------------
        foreach (\App\Support\Studio::services() as $i => $data) {
            Service::updateOrCreate(
                ['title' => $data['title']],
                [
                    'short_description' => $data['text'],
                    'full_description' => $data['text'],
                    'display_order' => $i + 1,
                    'is_published' => true,
                ]
            );
        }

        // ---- Journal --------------------------------------------------------
        foreach (\App\Support\Journal::all() as $data) {
            Article::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'category' => $data['category'],
                    'author' => 'Studio Volume',
                    'content' => '<p>'.implode('</p><p>', $data['body']).'</p>',
                    'excerpt' => $data['excerpt'],
                    'published_at' => \Illuminate\Support\Carbon::parse($data['date']),
                    'status' => 'published',
                    'seo_title' => $data['title'].' — Studio Volume Journal',
                    'seo_description' => $data['excerpt'],
                ]
            );
        }

        // ---- Designers ------------------------------------------------------
        $designers = [
            ['name' => 'Sopea Chan', 'position' => 'Principal Architect', 'years' => '14', 'specialization' => 'Residential & Cultural', 'bio' => 'Sopea founded the studio in 2014 and leads every project from the first sketch to the final detail.'],
            ['name' => 'Alex Morgan', 'position' => 'Senior Architect', 'years' => '11', 'specialization' => 'Residential architecture, sustainable design, material-driven spaces', 'bio' => 'Alex focuses on residential architecture, sustainable design, and material-driven spaces.'],
            ['name' => 'Dara Kim', 'position' => 'Interior Designer', 'years' => '8', 'specialization' => 'Interior architecture & furniture', 'bio' => 'Dara shapes the interior atmosphere of the studio’s houses, from joinery to light fixtures.'],
            ['name' => 'Mira Ly', 'position' => 'Architectural Visualizer', 'years' => '6', 'specialization' => 'Visualization & presentation', 'bio' => 'Mira translates drawings into atmospheres long before the first brick is laid.'],
        ];

        foreach ($designers as $i => $data) {
            Designer::updateOrCreate(
                ['name' => $data['name']],
                [
                    'position' => $data['position'],
                    'years_experience' => $data['years'],
                    'specialization' => $data['specialization'],
                    'bio' => $data['bio'],
                    'display_order' => $i + 1,
                    'is_published' => true,
                ]
            );
        }
    }
}

