<?php

namespace Database\Seeders\RiskManagement;

use App\Models\RiskManagement\RiskCategory;
use Illuminate\Database\Seeder;

class RiskCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (RiskCategory::count() == 0) {
            RiskCategory::insert([
                ['name' => 'Information Security Management and Governance', 'order_number' => 1],
                ['name' => 'Human Resources Security', 'order_number' => 2],
                ['name' => 'Information and Asset Management', 'order_number' => 3],
                ['name' => 'Access Control', 'order_number' => 4],
                ['name' => 'System acquisition, development and maintenance', 'order_number' => 5],
                ['name' => 'Environmental and Physical Security', 'order_number' => 6],
                ['name' => 'Operations, Systems and Communication Management', 'order_number' => 7],
                ['name' => 'Supplier Relationships', 'order_number' => 8],
                ['name' => 'Incident Management', 'order_number' => 9],
                ['name' => 'Business Continuity', 'order_number' => 10],
                ['name' => 'Compliance and Audit', 'order_number' => 11],
                ['name' => 'Cloud Security', 'order_number' => 12],
                ['name' => 'Other', 'order_number' => 13],
            ]);
        }
    }
}
