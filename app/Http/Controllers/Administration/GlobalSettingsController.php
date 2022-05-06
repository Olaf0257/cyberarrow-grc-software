<?php

namespace App\Http\Controllers\Administration;

use Inertia\Inertia;
use App\Traits\Timezone;
use LdapRecord\Container;
use App\Saml2Sp\Saml2Auth;
use Illuminate\Http\Request;
use App\Utils\RegularFunctions;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use OneLogin\Saml2\IdPMetadataParser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\LicenseBox\LicenseBoxExternalAPI;
use Illuminate\Support\Facades\Validator;
use App\Models\GlobalSettings\LdapSetting;
use App\Models\GlobalSettings\MailSetting;
use App\Models\GlobalSettings\SamlSetting;
use App\Models\GlobalSettings\GlobalSetting;
use Illuminate\Validation\ValidationException;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixScore;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixImpact;
use App\Models\RiskManagement\RiskMatrix\RiskScoreLevelType;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixLikelihood;
use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\RiskManagement\RiskMatrix\RiskMatrixAcceptableScore;

class GlobalSettingsController extends Controller
{
    use Timezone;

    public function index(Request $request)
    {
        $sessionExpiryTimes = [
            'null' => 'Never',  
            '15' => '15 minutes',
            '30' => '30 minutes',
            '60' => '1 hour',
        ];

        // Timezone array from Timezone Traits
        $timezones = $this->appTimezone();

        // mail settings
        $mailSettings = MailSetting::first();
        $is_mail_testable = true;

        if(!$mailSettings){
            $mailSettings = new MailSetting;
            $mailSettings->mail_host = 'mailhog';
            $mailSettings->mail_port = '1025';
            $mailSettings->mail_encryption = strtolower('ssl');
            $mailSettings->mail_from_address = env('MAIL_FROM_ADDRESS', 'grc@ebdaa.ae');
            $mailSettings->mail_from_name = env('MAIL_FROM_NAME', 'CyberArrow GRC');
            $is_mail_testable = false;
        }

        // ldap settings
        $ldapSettings = LdapSetting::first();

        // ldap settings
        $samlSetting = SamlSetting::first();

        // organization settings
        $organizations = Organization::with(['departments' => function ($query) {
            $query->where('parent_id', 0);
        }])->get();


        /* Risk matrix likelihoods */
        $riskMatrixLikelihoods = RiskMatrixLikelihood::all(['id', 'name', 'index']);
        $riskMatrixImpacts = RiskMatrixImpact::all(['id', 'name', 'index']);
        $riskMatrixScores = RiskMatrixScore::orderBy('likelihood_index', 'ASC')
            ->orderBy('impact_index', 'ASC')->select(['id', 'score', 'impact_index', 'likelihood_index'])->get()->split(count($riskMatrixLikelihoods));
        $riskScoreLevelTypes = RiskScoreLevelType::with(['levels' => function ($query) {
            $query->select('id', 'name', 'max_score', 'color', 'level_type');
        }])->select(['id', 'level', 'is_active'])->get();
        $riskMatrixAcceptableScore = RiskMatrixAcceptableScore::select('id', 'score')->first();

        $license=[];
        /** License Detail */
        if(env('LICENSE_ENABLED')){
            $licenseDetails = new LicenseBoxExternalAPI();
            $license['currentVersion'] = $licenseDetails->get_current_version();
            $verificationWithDetails = $licenseDetails->verify_license();
            $license['licensedTo'] = ucFirst(Config::get('license.license.client_name'));
           
            $license['licenseExpiryDate'] = $verificationWithDetails['data'];
        }
        
        return Inertia::render('global-settings/GlobalSettings', [
            'timezones' => $timezones,
            'sessionExpiryTimes' => $sessionExpiryTimes,
            'mailSettings' => $mailSettings,
            'ldapSettings' => $ldapSettings,
            'samlSetting' => $samlSetting,
            'organizations' => $organizations,
            'riskMatrixLikelihoods' => $riskMatrixLikelihoods,
            'riskMatrixImpacts' => $riskMatrixImpacts,
            'riskMatrixScores' => $riskMatrixScores,
            'riskScoreLevelTypes' => $riskScoreLevelTypes,
            'riskMatrixAcceptableScore' => $riskMatrixAcceptableScore,
            'is_mail_testable' => $is_mail_testable,
            'license'=>$license,
            'form_actions' => [
                'global_settings' => route('global-settings.store'),
                'mail_settings' => route('global-settings.mail-settings'),
                'ldap_settings' => route('global-settings.ldap-settings'),
                'saml_settings' => [
                    'upload' => route('global-settings.saml-settings.saml-provider-metadata.upload'),
                    'remote_import' => route('global-settings.saml-settings.saml-provider-metadata.import'),
                    'remove' => route('global-settings.saml-settings.saml-provider-metadata.remove'),
                    'manual' => route('global-settings.saml-settings')
                ]
            ],
            'connection_test_routes' => [
                'mail_settings' => route('global-settings.test-mail-connection'),
                'ldap_settings' => route('global-settings.test-ldap-connection')
            ],
            'saml_information' => [
                'metadata' => route('saml2.metadata'),
                'acs' => route('saml2.acs'),
                'login' => route('saml2.login'),
                'sls' => route('saml2.sls'),
                'download' => route('global-settings.saml-settings.download.sp-metadata'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|max:191',
            'primary_color' => 'required|max:191',
            'secondary_color' => 'required|max:191',
            'default_text_color' => 'required|max:191',
            'company_logo' => 'nullable|image|dimensions:min_width=70,min_height=71,max_width=595,max_height=600',
            'small_company_logo' => 'nullable|image',
            'favicon' => 'nullable|image',
            'allow_document_upload' => 'required',
            'allow_document_link' => 'required',
            'session_timeout' => 'required|in:null,15,30,60',
        ], [
            'display_name.required' => 'The Display Name field is required',
            'primary_color.required' => 'The Primary Color field is required',
            'secondary_color.required' => 'The Secondary Color field is required',
            'default_text_color.required' => 'The Default Text Color field is required',
            'allow_document_upload.required' => 'The Allow Document Upload field is required',
            'allow_document_link.required' => 'The Allow Document Link field is required',
        ]);

        $inputs = $request->only('display_name', 'timezone', 'primary_color', 'secondary_color', 'default_text_color', 'secure_mfa_login', 'allow_document_upload', 'allow_document_link');

        if ($request->session_timeout == 'null') {
            $inputs['session_timeout'] = null;
        } else {
            $inputs['session_timeout'] = $request->session_timeout;
        }

        $globalSetting = GlobalSetting::first();

        if ($request->hasFile('company_logo')) {
            $companyLogoPath = $request->file('company_logo')->store(
                'public/global_settings/' . $request->user()->id
            );

            $pathArray = explode('/', $companyLogoPath);
            $pathArray[0] = '';

            $inputs['company_logo'] = implode('/', $pathArray);
        }

        if ($request->hasFile('small_company_logo')) {
            $smallCompanyLogoPath = $request->file('small_company_logo')->store(
                'public/global_settings/' . $request->user()->id
            );

            $pathArray = explode('/', $smallCompanyLogoPath);
            $pathArray[0] = '';

            $inputs['small_company_logo'] = implode('/', $pathArray);
        }

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon')->store(
                'public/global_settings/' . $request->user()->id
            );

            $pathArray = explode('/', $favicon);
            $pathArray[0] = '';

            $inputs['favicon'] = implode('/', $pathArray);
        }

        $globalSetting->fill($inputs);

        $updated = $globalSetting->update();

        Log::info('User has updated global settings');
        if ($updated) {
            return redirect()->back()->with([
                'success' => 'Global settings updated successfully.',
                'activeTab' => 'globalSettings',
            ]);
        }
    }

    public function updateMailSetting(Request $request)
    {
        $mailSetting = MailSetting::first();

        $mailSetting = $mailSetting ?: new MailSetting();

        $request->validate([
            'mail_host' => 'required',
            'mail_port' => 'required|integer',
            'mail_username' => 'required',
            'mail_encryption' => 'required',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required',
            'mail_password' => [function ($attribute, $value, $fail) use ($mailSetting, $request) {
                if (is_null($mailSetting->mail_password) && !$request->mail_password) {
                    $fail('The SMTP Password field is required');
                }
            }],
        ], [
            'mail_host.required' => 'The SMTP Host field is required',
            'mail_port.required' => 'The SMTP Port field is required',
            'mail_username.required' => 'The SMTP Username field is required',
            'mail_encryption.required' => 'The SMTP Encryption field is required',
            'mail_from_address.required' => 'The From Address field is required',
            'mail_from_name.required' => 'The From Name field is required',
        ]);

        $inputs = $request->only('mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name');

        $mailSetting->fill($inputs);

        $updated = $mailSetting->save();

        if ($updated) {
            Log::info('User has updated SMTP Settings');
            return redirect()->back()->with([
                'success' => 'SMTP Settings updated successfully.',
                'activeTab' => 'smtpSettings',
            ]);
        }
    }

    public function testMailConnection(Request $request)
    {
        Log::info('User is testing mail connection');
        try {
            $mailConfig = \Config::get('mail');

            if (!$mailConfig['host'] || !$mailConfig['port'] || !$mailConfig['encryption'] || !$mailConfig['username'] || !$mailConfig['password']) {
                return redirect()->back()->with([
                    'error' => 'SMTP Settings are not configured.',
                    'activeTab' => 'smtpSettings',
                ]);
            }

            // Create the Transport
            $transport = (new \Swift_SmtpTransport($mailConfig['host'], $mailConfig['port'], $mailConfig['encryption']))
                ->setUsername($mailConfig['username'])
                ->setPassword($mailConfig['password']);

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);

            $mailer->getTransport()->start();

            Log::info('User mail connection succeeded');
            return redirect()->back()->with([
                'success' => 'Connection to SMTP established successfully.',
                'activeTab' => 'smtpSettings',
            ]);
        } catch (\Exception $exception) {

            Log::info('User mail connection failed');
            return redirect()->back()->with([
                'error' => 'Failed to process request. Please check SMTP authentication connection.',
                'activeTab' => 'smtpSettings',
            ]);
        }
    }

    public function testLdapConnection(Request $request)
    {
        Log::info('User is testing LDAP connection');
        try {
            // ldap settings
            $ldapSettings = LdapSetting::first();

            $connection = Container::getConnection('ldap');

            $auth = $connection->auth()->attempt($ldapSettings->base_dn, $ldapSettings->password);

            // verify binding
            if ($auth) {
                Log::info('User LDAP connection succeeded');
                return redirect()->back()->with([
                    'success' => 'Connection to LDAP established successfully.',
                    'activeTab' => 'ldapSettings',
                ]);
            } else {
                Log::info('User LDAP connection failed');
                return redirect()->back()->with([
                    'error' => 'Failed to process request. Please check LDAP authentication connection.',
                    'activeTab' => 'ldapSettings',
                ]);
            }
        } catch (\Exception $exception) {
            Log::info('User LDAP connection failed');
            return redirect()->back()->with([
                'error' => 'Failed to process request. Please check LDAP authentication connection.',
                'activeTab' => 'ldapSettings',
            ]);
        }
    }

    public function updateLdapSetting(Request $request)
    {
        $request->validate([
            'hosts' => 'required',
            'base_dn' => 'required',
            'username' => 'required',
            'bind_password' => 'required',
            'port' => 'nullable|integer',
            'use_ssl' => 'nullable|in:1,0',
            'version' => 'nullable|integer',
            'map_first_name_to' => 'required',
            'map_last_name_to' => 'required',
            'map_email_to' => 'required',
        ], [
            'bind_password.required' => 'The password field is required',
        ]);

        $ldapSettings = LdapSetting::first();

        $inputs = $request->toArray();
        $inputs['use_ssl'] = isset($inputs['use_ssl']) ? ($inputs['use_ssl'] == '1' ? true : false) : false;
        $inputs['password'] = $request->bind_password;

        if (is_null($ldapSettings)) {
            $created = LdapSetting::create($inputs);
            Log::info('User has updated LDAP Settings');

            return redirect()->back()->with([
                'success' => 'LDAP setting configured successfully.',
                'activeTab' => 'ldapSettings',
            ]);
        }

        $updated = $ldapSettings->update($inputs);

        if (!$updated) {
            // code...
            Log::info('User could not update LDAP Settings');
        }
        Log::info('User has updated LDAP Settings');

        return redirect()->back()->with([
            'success' => 'LDAP setting updated successfully.',
            'activeTab' => 'ldapSettings',
        ]);
    }

    public function updateSamlSetting(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'sso_provider' => 'required',
            'entity_id' => 'required',
            'sso_url' => 'required',
            'slo_url' => 'required',
            'certificate' => 'required',
        ]);
        if($validator->fails()){
            return redirect()->back()->with([
                'activeTab' => 'samlSettings'
            ])->withErrors($validator)->withInput();
        }

        $samlSetting = SamlSetting::first();

        $samlSetting = $samlSetting ?: new SamlSetting();

        $inputs = $request->only('sso_provider', 'entity_id', 'sso_url', 'slo_url', 'certificate');

        $samlSetting->fill($inputs);

        $updated = $samlSetting->save();

        if ($updated) {
            Log::info('User has updated SAML Settings');
            return redirect()->back()->with([
                'success' => 'SAML settings updated successfully.',
                'activeTab' => 'samlSettings',
            ]);
        }
    }

    /***
     * upload Identity provider metadata
     */
    public function uploadSamlProviderMetadata(Request $request)
    {
        Log::info('User is attempting to upload SAML provider metadata');
        $request->validate([
            'saml_provider_metadata_file' => 'required|file|mimes:xml',
        ]);

        $metadataInfo = IdPMetadataParser::parseFileXML($request->file('saml_provider_metadata_file'));

        return $this->updateSamlSettingsFromMetadata($metadataInfo, 'file_upload');
    }

    /***
     * import Identity provider metadata
     */
    public function importSamlProviderMetadata(Request $request)
    {
        Log::info('User is attempting to import remote SAML provider metadata');
        $request->validate([
            'saml_provider_remote_metadata' => 'required|url',
        ], [
            'saml_provider_remote_metadata.url' => 'The saml provider remote metadata field must be a url',
        ]);

        $metadataInfo = IdPMetadataParser::parseRemoteXML($request->saml_provider_remote_metadata);

        return $this->updateSamlSettingsFromMetadata($metadataInfo, 'url_import');
    }

    /**
     *  helper method used to update saml settings.
     */
    private function updateSamlSettingsFromMetadata($metadataInfo, $metadataSource)
    {
        $errorsMsgs = [];

        if (empty($metadataInfo)) {
            $errorMsg = ['Uploaded metadata is not a valid Identity provider metadata '];

            if ($metadataSource == 'file_upload') {
                $errorsMsgs['saml_provider_metadata_file'] = $errorMsg;
            } else {
                $errorsMsgs['saml_provider_remote_metadata'] = $errorMsg;
            }

            throw ValidationException::withMessages($errorsMsgs);
        }

        $metadataInfo = $metadataInfo['idp'];

        if (empty($metadataInfo['entityId'])) {
            $errorMsg = ['Uploaded metadata is missing entityId'];

            if ($metadataSource == 'file_upload') {
                $errorsMsgs['saml_provider_metadata_file'] = $errorMsg;
            } else {
                $errorsMsgs['saml_provider_remote_metadata'] = $errorMsg;
            }
        }

        // sso service key validation
        if (empty($metadataInfo['singleSignOnService']) || empty($metadataInfo['singleSignOnService']['url'])) {
            $errorMsg = ['Uploaded metadata is missing singleSignOnService'];

            if ($metadataSource == 'file_upload') {
                $errorsMsgs['saml_provider_metadata_file'] = $errorMsg;
            } else {
                $errorsMsgs['saml_provider_remote_metadata'] = $errorMsg;
            }
        }

        // slo service key validation
        if (empty($metadataInfo['singleLogoutService']) || empty($metadataInfo['singleLogoutService']['url'])) {
            $errorMsg = ['Uploaded metadata is missing singleLogoutService'];

            if ($metadataSource == 'file_upload') {
                $errorsMsgs['saml_provider_metadata_file'] = $errorMsg;
            } else {
                $errorsMsgs['saml_provider_remote_metadata'] = $errorMsg;
            }
        }

        // x509cert validation

        $certificate = '';
        $is_x509certMulti = false;

        if (isset($metadataInfo['x509cert'])) {
            $certificate = $metadataInfo['x509cert'];
        } elseif (isset($metadataInfo['x509certMulti'])) {
            $certificate = json_encode($metadataInfo['x509certMulti']);
            $is_x509certMulti = true;
        } else {
            if (empty($metadataInfo['x509cert'])) {
                $errorMsg = ['Uploaded metadata is missing x509cert'];

                if ($metadataSource == 'file_upload') {
                    $errorsMsgs['saml_provider_metadata_file'] = $errorMsg;
                } else {
                    $errorsMsgs['saml_provider_remote_metadata'] = $errorMsg;
                }
            }
        }

        if (count($errorsMsgs) > 0) {
            throw ValidationException::withMessages($errorsMsgs);
        }

        // creating if already does not exist
        $samlSetting = SamlSetting::first();
        $samlSetting = $samlSetting ?: new SamlSetting();

        $samlSetting->fill([
            'sso_provider' => 'Update this field with correct sso provider',
            'entity_id' => $metadataInfo['entityId'],
            'sso_url' => $metadataInfo['singleSignOnService']['url'],
            'slo_url' => $metadataInfo['singleLogoutService']['url'],
            'certificate' => $certificate,
            'is_x509certMulti' => $is_x509certMulti,
        ]);

        $updated = $samlSetting->save();

        if ($updated) {
            Log::info('User has uploaded SAML provider metadata');
            return redirect()->back()->with([
                'success' => 'SAML settings updated successfully.',
                'activeTab' => 'samlSettings',
            ]);
        }
    }

    /**
     * download sp metadata.
     */
    public function downloadSpMetadata(Saml2Auth $saml2Auth)
    {
        Log::info('User has downloaded SAML Metadata');
        $contents = $saml2Auth->getMetadata();
        $filename = 'metadata.xml';

        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, $filename);
    }


    /**
     * download sp metadata.
     */
    public function removeSamlSettings()
    {
        SamlSetting::truncate();
        Log::info('User has removed SAML provider metadata');
        return back()->with([
            'success' => 'SAML settings removed successfully.',
            'activeTab' => 'samlSettings',
        ]);
    }
}
