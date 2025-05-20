<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Hair Styling',
                'description' => 'Professional hair styling services',
                'title' => 'Hair Styling',
                'slug' => 'hair-styling',
            ],
            [
                'name' => 'Hair Coloring',
                'description' => 'Expert hair coloring and treatment services',
                'title' => 'Hair Coloring',
                'slug' => 'hair-coloring',
            ],
            [
                'name' => 'Men\'s Grooming',
                'description' => 'Specialized services for men\'s hair and grooming',
                'title' => 'Men\'s Grooming',
                'slug' => 'mens-grooming',
            ],
            [
                'name' => 'Facial Treatments',
                'description' => 'Rejuvenating facial treatments for all skin types',
                'title' => 'Facial Treatments',
                'slug' => 'facial-treatments',
            ],
            [
                'name' => 'Massage Therapy',
                'description' => 'Relaxing and therapeutic massage services',
                'title' => 'Massage Therapy',
                'slug' => 'massage-therapy',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 