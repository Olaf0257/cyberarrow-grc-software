<?php

namespace Database\Seeders\Integration;

use App\Models\Integration\IntegrationCategory;
use Illuminate\Database\Seeder;

class IntegrationCategorySeeder extends Seeder
{
    protected $categories = [
        [
            'id' => 1,
            'name' => 'Business Suite'
        ],
        [
            'id' => 2,
            'name' => 'SSO'
        ],
        [
            'id' => 3,
            'name' => 'Cloud Services'
        ],
        [
            'id' => 4,
            'name' => 'Development Tools'
        ],
        [
            'id' => 5,
            'name' => 'Ticketing'
        ],
        [
            'id' => 6,
            'name' => 'Device Management'
        ],
        [
            'id' => 7,
            'name' => 'Asset Management and Helpdesk'
        ],
        [
            'id' => 8,
            'name' => 'SDLC'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->categories as $key=>$category){
            $category['order_number'] = $key + 1;

            IntegrationCategory::insert($category);
        }
    }
}
