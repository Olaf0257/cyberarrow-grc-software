<?php

namespace Database\Seeders\ThirdPartyRisk;

use App\Models\ThirdPartyRisk\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;

class IndustriesSeeder extends Seeder
{
    public array $industries = [
        "Accounting",
        "Architecture & Planning",
        "Automotive",
        "Aviation & Aerospace",
        "Banking",
        "Biotechnology",
        "Building Materials",
        "Civic & Social Organization",
        "Civil Engineering",
        "Computer & Network Security",
        "Computer Software",
        "Construction",
        "Defense & Space",
        "Design",
        "Education",
        "Entertainment",
        "Environmental Services",
        "Events Services",
        "Financial Services",
        "Food & Beverages",
        "Government Administration",
        "Graphic Design",
        "Health, Wellness and Fitness",
        "Hospital & Health Care",
        "Hospitality",
        "Information Technology and Services",
        "Insurance",
        "Internet",
        "Law Practice",
        "Leisure, Travel & Tourism",
        "Management Consulting",
        "Marketing and Advertising",
        "Mechanical or Industrial Engineering",
        "Non-Profit Organization Management",
        "Oil & Energy",
        "Real Estate",
        "Renewables & Environment",
        "Retail",
        "Sports",
        "Staffing and Recruiting",
        "Telecommunications",
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->industries as $key => $industry) {
            Industry::insert([
                'name' => $industry,
                'order_number' => $key + 1
            ]);
        }
    }
}
