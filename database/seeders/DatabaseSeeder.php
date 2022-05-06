<?php

namespace Database\Seeders;

use Database\Seeders\ThirdPartyRisk\FixQuestionTypos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Compliance\Standard;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Admin\AddNewAdminRoleSeeder;
use Database\Seeders\RiskManagement\NCAECCSeeder;
use Database\Seeders\Compliance\PciDssFixesSeeder;
use Database\Seeders\RiskManagement\NCACSCCSeeder;
use Database\Seeders\ThirdPartyRisk\DomainsSeeder;
use Database\Seeders\Integration\IntegrationSeeder;
use Database\Seeders\RiskManagement\ISRRisksSeeder;
use Database\Seeders\ThirdPartyRisk\IndustriesSeeder;
use Database\Seeders\RiskManagement\ISO27002RisksSeeder;
use Database\Seeders\RiskManagement\RiskStandardsSeeder;
use Database\Seeders\RiskManagement\RiskCategoriesSeeder;
use Database\Seeders\Compliance\Iso2019AndNcema2021Seeder;
use Database\Seeders\Compliance\PciDssAppendixAFixesSeeder;
use Database\Seeders\Integration\IntegrationCategorySeeder;
use Database\Seeders\ThirdPartyRisk\FixQuestionnaireSeeder;
use Database\Seeders\Compliance\NistCybersecurityFixesSeeder;
use Database\Seeders\ThirdPartyRisk\DefaultQuestionnaireSeeder;
use Database\Seeders\Compliance\DefaultComplianceStandardsSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\ScoreDefaultSeeder;
use Database\Seeders\Administration\Settings\RevertDefaultMailSeed;
use Database\Seeders\RiskManagement\RiskMatrix\ImpactDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskScoreLevelsSeeder;
use Database\Seeders\RiskManagement\SamaCyberSecurityFrameworkSeeder;
use Database\Seeders\Admin\DefaultOrganization as DefaultOrganization;
use Database\Seeders\Administration\Settings\DefaultMailSettingsSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\LikelihoodDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskAcceptableScoreSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\RiskScoreLevelTypesSeeder;
use Database\Seeders\RiskManagement\StandardSeeders\UAEIASeeder as RiskStandardUAEIASeeder;
use Database\Seeders\Compliance\DefaultStandardSeeders\UAEIASeeder as ComplianceStandardUAEIASeeder;

class DatabaseSeeder extends Seeder
{
    private $seeders = [
        // Default compliance seeder
        [
            'name' => 'DefaultComplianceStandardsSeeder',
            'class' => DefaultComplianceStandardsSeeder::class
        ],
        [
            'name' => 'ComplianceStandardUAEIASeeder',
            'class' => ComplianceStandardUAEIASeeder::class
        ],
        /* Seeding risk management module */
        [
            'name' => 'RiskStandardsSeeder',
            'class' => RiskStandardsSeeder::class
        ],
        [
            'name' => 'RiskCategoriesSeeder',
            'class' => RiskCategoriesSeeder::class
        ],
        [
            'name' => 'ISO27002RisksSeeder',
            'class' => ISO27002RisksSeeder::class
        ],
        [
            'name' => 'ISRRisksSeeder',
            'class' => ISRRisksSeeder::class
        ],
        [
            'name' => 'NCACSCCSeeder',
            'class' => NCACSCCSeeder::class
        ],
        [
            'name' => 'NCAECCSeeder',
            'class' => NCAECCSeeder::class
        ],
        [
            'name' => 'SamaCyberSecurityFrameworkSeeder',
            'class' => SamaCyberSecurityFrameworkSeeder::class
        ],
        [
            'name' => 'RiskStandardUAEIASeeder',
            'class' => RiskStandardUAEIASeeder::class
        ],
        /* Default risk matrix seeder */
        [
            'name' => 'ImpactDefaultSeeder',
            'class' => ImpactDefaultSeeder::class
        ],
        [
            'name' => 'LikelihoodDefaultSeeder',
            'class' => LikelihoodDefaultSeeder::class
        ],
        [
            'name' => 'ScoreDefaultSeeder',
            'class' => ScoreDefaultSeeder::class
        ],
        [
            'name' => 'RiskScoreLevelTypesSeeder',
            'class' => RiskScoreLevelTypesSeeder::class
        ],
        [
            'name' => 'RiskScoreLevelsSeeder',
            'class' => RiskScoreLevelsSeeder::class
        ],
        [
            'name' => 'RiskAcceptableScoreSeeder',
            'class' => RiskAcceptableScoreSeeder::class
        ],
        /*Just for multi tenant*/
        [
            'name' => 'DefaultMailSettingsSeeder',
            'class' => DefaultMailSettingsSeeder::class
        ],
        [
            'name' => 'Iso2019AndNcema2021Seeder',
            'class' => Iso2019AndNcema2021Seeder::class
        ],
        [
            'name' => 'NistCybersecurityFixesSeeder',
            'class' => NistCybersecurityFixesSeeder::class
        ],
        [
            'name' => 'PciDssFixesSeeder',
            'class' => PciDssFixesSeeder::class
        ],
        [
            'name' => 'DefaultOrganization',
            'class' => DefaultOrganization::class
        ],
        [
            'name' => 'AddNewAdminRoleSeeder',
            'class' => AddNewAdminRoleSeeder::class
        ],
        [
            'name' => 'DomainsSeeder',
            'class' => DomainsSeeder::class
        ],
        [
            'name' => 'DefaultQuestionnaireSeeder',
            'class' => DefaultQuestionnaireSeeder::class
        ],
        [
            'name' => 'IndustriesSeeder',
            'class' => IndustriesSeeder::class,
        ],
        [
            'name' => 'IntegrationCategorySeeder',
            'class' => IntegrationCategorySeeder::class,
        ],
        [
            'name' => 'IntegrationSeeder',
            'class' => IntegrationSeeder::class,
        ],
        [
            'name' => 'FixQuestionnaireSeeder',
            'class' => FixQuestionnaireSeeder::class,
        ],
        [
            'name' => 'RevertDefaultMailSeed',
            'class' => RevertDefaultMailSeed::class,
        ],
        [
            'name' => 'FixQuestionTypos',
            'class' => FixQuestionTypos::class,
        ]
    ];
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        foreach ($this->seeders as $seeder) {
            $seeded = \DB::table('seeders')->where('seeder', $seeder['name'])->first();

            if (!$seeded) {
                $this->call($seeder['class']);

                /* Creating seeder record in seeder table */
                \DB::table('seeders')->insert([
                    'seeder' => $seeder['name']
                ]);
            }
        }
    }
}
