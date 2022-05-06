<?php

namespace Database\Seeders\Integration;

use App\Models\Integration\Integration;
use App\Models\Integration\IntegrationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegrationSeeder extends Seeder
{
    protected $services = [
        [
            "name" => "Google Workspace",
            'category_id' => IntegrationCategory::BUSINESS_SUIT_ID,
            'logo' => 'google-drive.png',
        ],
        [
            'name' => 'Office 365',
            'category_id' => IntegrationCategory::BUSINESS_SUIT_ID,
            'logo' => 'office-365.png',
        ],
        [
            'name' => 'Google cloud identity',
            'category_id' => IntegrationCategory::SSO_ID,
            'logo' => 'google-cloud.png',
        ],
        [
            'name' => 'Okta',
            'category_id' => IntegrationCategory::SSO_ID,
            'logo' => 'okta.png',
        ],
        [
            'name' => 'Azure Active Directory',
            'category_id' => IntegrationCategory::SSO_ID,
            'logo' => 'azure.png',
        ],
        [
            'name' => 'Auth0',
            'category_id' => IntegrationCategory::SSO_ID,
            'logo' => 'auth0.png',
        ],
        [
            'name' => 'AWS',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'aws.png',
        ],
        [
            'name' => 'Google Cloud',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'google-cloud.png',
        ],
        [
            'name' => 'Azure',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'azure.png',
        ],
        [
            'name' => 'Heroku',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'heroku.png',
        ],
        [
            'name' => 'DigitalOcean',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'digital-ocean.png',
        ],
        [
            'name' => 'Cloudflare',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'cloudflare.png',
        ],
        [
            'name' => 'MongoDB',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'mongodb.png',
        ],
        [
            'name' => 'OracleCloud',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'oracle.png',
        ],
        [
            'name' => 'Veeam',
            'category_id' => IntegrationCategory::CLOUD_SERVICES_ID,
            'logo' => 'veeam.png',
        ],
        [
            'name' => 'GitLab',
            'category_id' => IntegrationCategory::DEVELOPMENT_TOOLS_ID,
            'logo' => 'gitlab.png',
        ],
        [
            'name' => 'Github',
            'category_id' => IntegrationCategory::DEVELOPMENT_TOOLS_ID,
            'logo' => 'github.png',
        ],
        [
            'name' => 'Bitbucket',
            'category_id' => IntegrationCategory::DEVELOPMENT_TOOLS_ID,
            'logo' => 'bitbucket.png',
        ],
        [
            'name' => 'Azure Devops',
            'category_id' => IntegrationCategory::DEVELOPMENT_TOOLS_ID,
            'logo' => 'azure-devops.png',
        ],
        [
            'name' => 'Linear',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'linear.png',
        ],
        [
            'name' => 'Github issues',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'github.png',
        ],
        [
            'name' => 'Gitlab issues',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'gitlab.png',
        ],
        [
            'name' => 'Jira',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'Jira.png',
        ],
        [
            'name' => 'Zoho',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'Zoho.png',
        ],
        [
            'name' => 'Pivotal Tracker',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'PivotalTrackerWhite.png',
        ],
        [
            'name' => 'Shortcut',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'Shortcut.png',
        ],
        [
            'name' => 'Clickup',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'clickup.png',
        ],
        [
            'name' => 'Targetprocess',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'TargetProcess.png',
        ],
        [
            'name' => 'Azure Boards',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'azureBoards.png',
        ],
        [
            'name' => 'Trello',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'Trello.png',
        ],
        [
            'name' => 'Asana',
            'category_id' => IntegrationCategory::TICKETING_ID,
            'logo' => 'asana.png',
        ],
        [
            'name' => 'Intune',
            'category_id' => IntegrationCategory::DEVICE_MANAGEMENT_ID,
            'logo' => 'intune.png',
        ],
        [
            'name' => 'Jamf',
            'category_id' => IntegrationCategory::DEVICE_MANAGEMENT_ID,
            'logo' => 'Jamf.png',
        ],
        [
            'name' => 'CyberArrow Agent',
            'category_id' => IntegrationCategory::DEVICE_MANAGEMENT_ID,
            'logo' => 'agent.png',
        ],
        [
            'name' => 'ManageEngine',
            'category_id' => IntegrationCategory::ASSET_MANAGEMENT_AND_HELPDESK_ID,
            'logo' => 'manageengine.png',
        ],
        [
            'name' => 'Ivanti',
            'category_id' => IntegrationCategory::ASSET_MANAGEMENT_AND_HELPDESK_ID,
            'logo' => 'ivanti.jpg',
        ],
        [
            'name' => 'BMC Remedy',
            'category_id' => IntegrationCategory::ASSET_MANAGEMENT_AND_HELPDESK_ID,
            'logo' => 'bmc-remedy.png',
        ],
        [
            'name' => 'ServiceNow',
            'category_id' => IntegrationCategory::ASSET_MANAGEMENT_AND_HELPDESK_ID,
            'logo' => 'ServiceNow.png',
        ],
        [
            'name' => 'SonarCloud',
            'category_id' => IntegrationCategory::SDLC_ID,
            'logo' => 'sonarcloud.svg',
        ],
        [
            'name' => 'SonarQube',
            'category_id' => IntegrationCategory::SDLC_ID,
            'logo' => 'SonarQube.png',
        ],
        [
            'name' => 'Tenable',
            'category_id' => IntegrationCategory::SDLC_ID,
            'logo' => 'tenable.png',
        ],
        [
            'name' => 'Nexpose',
            'category_id' => IntegrationCategory::SDLC_ID,
            'logo' => 'nexpose.png',
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('integrations')->truncate();

        foreach ($this->services as $service){
            $service['description'] = "Integrating \"". $service['name'] ."\" instead of manually collecting evidence saves companies hundreds of hours per year.";

            Integration::insert($service);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
