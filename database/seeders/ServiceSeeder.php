<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            'Hair Styling' => [
                [
                    'title' => 'Women\'s Haircut',
                    'excerpt' => 'Professional haircut for women',
                    'duration' => 60,
                    'price' => 50.00,
                ],
                [
                    'title' => 'Blow Dry',
                    'excerpt' => 'Professional blow dry styling',
                    'duration' => 45,
                    'price' => 35.00,
                ],
            ],
            'Hair Coloring' => [
                [
                    'title' => 'Full Color',
                    'excerpt' => 'Complete hair coloring service',
                    'duration' => 120,
                    'price' => 80.00,
                ],
                [
                    'title' => 'Highlights',
                    'excerpt' => 'Professional hair highlighting',
                    'duration' => 90,
                    'price' => 65.00,
                ],
            ],
            'Men\'s Grooming' => [
                [
                    'title' => 'Men\'s Haircut',
                    'excerpt' => 'Professional haircut for men',
                    'duration' => 30,
                    'price' => 30.00,
                ],
                [
                    'title' => 'Beard Trim',
                    'excerpt' => 'Professional beard trimming and shaping',
                    'duration' => 20,
                    'price' => 20.00,
                ],
            ],
            [
                'name' => 'Haircut',
                'description' => 'Professional haircut service',
                'title' => 'Haircut',
                'slug' => 'haircut',
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Expert hair coloring service',
                'title' => 'Hair Coloring',
                'slug' => 'hair-coloring',
            ],
            [
                'name' => 'Facial',
                'description' => 'Rejuvenating facial treatment',
                'title' => 'Facial',
                'slug' => 'facial',
            ],
            [
                'name' => 'Massage',
                'description' => 'Relaxing massage therapy',
                'title' => 'Massage',
                'slug' => 'massage',
            ],
        ];

        foreach ($services as $categoryName => $categoryServices) {
            $category = Category::where('name', $categoryName)->first();
            
            if ($category) {
                foreach ($categoryServices as $service) {
                    Service::create([
                        'category_id' => $category->id,
                        'title' => $service['title'],
                        'slug' => Str::slug($service['title']),
                        'excerpt' => $service['excerpt'],
                        'duration' => $service['duration'],
                        'price' => $service['price'],
                    ]);
                }
            }
        }
    }
} 