<?php

namespace App\Nova\Model;

use Exception;
use Laravel\Cashier\Billable;
use App\Nova\Helpers\CloudflareHelper;
use App\Models\GlobalSettings\MailSetting;
use App\Exceptions\NoPrimaryDomainException;
use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\MaintenanceMode;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * @property-read string $plan_name The tenant's subscription plan name
 * @property-read bool $on_active_subscription Is the tenant actively subscribed (not on grace period)
 * @property-read bool $can_use_app Can the tenant use the application (is on trial or subscription)
 * 
 * @property-read Domain[]|Collection $domains
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, Billable, MaintenanceMode;

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'email',
            'stripe_id',
            'card_brand',
            'card_last_four',
            'trial_ends_at',
        ];
    }

    public function primary_domain()
    {
        return $this->hasOne(Domain::class)->where('is_primary', true);
    }

    public function fallback_domain()
    {
        return $this->hasOne(Domain::class)->where('is_fallback', true);
    }

    public function route($route, $parameters = [], $absolute = true)
    {
        if (! $this->primary_domain) {
            throw new Exception('No any primary domain found.');
        }

        $domain = $this->primary_domain->domain;
        
        $parts = explode('.', $domain);
        if (count($parts) === 1) { // If subdomain
            $domain = Domain::domainFromSubdomain($domain);
        }

        return tenant_route($domain, $route, $parameters, $absolute);
    }

    public function impersonationUrl($user_id): string
    {
        $token = tenancy()->impersonate($this, $user_id, $this->route('tenant.home'), 'web')->token;

        return $this->route('tenant.impersonate', ['token' => $token]);
    }

    /**
     * Get the tenant's subscription plan name.
     *
     * @return string
     */
    public function getPlanNameAttribute(): string
    {
        return config('saas.plans')[$this->subscription('default')->stripe_plan];
    }

    /**
     * Is the tenant actively subscribed (not on grace period).
     * 
     * @return string 
     */
    public function getOnActiveSubscriptionAttribute(): bool
    {
        return $this->subscribed('default') && ! $this->subscription('default')->cancelled();
    }

    /**
     * Can the tenant use the application (is on trial or subscription).
     *
     * @return boolean
     */
    public function getCanUseAppAttribute(): bool
    {
        return $this->onTrial() || $this->subscribed('default');
    }

    public static function boot()
    {
        parent::boot();
        
        // deleting cname on deleting of the tenant
        Tenant::deleting(function($tenant)
        {
            if(env('CLOUDFLARE_ENABLED')){
                $domains=$tenant->domains()->get();
                foreach($domains as $dmn){
                    CloudflareHelper::delete_dns_record_by_cname($dmn->domain);
                }
            }   
        });

        // deleting storage folder on tenant delete
        Tenant::deleted(function($tenant){
            $storage_folder=storage_path().'/'.config('tenancy.filesystem.suffix_base').$tenant->id;
            if(is_dir($storage_folder)){
                \File::deleteDirectory($storage_folder);
            }
        });
    }
    // config mapping for multitenant
    public function getMailAttribute(){
        try{
            $mail=MailSetting::first();
            if($mail){
                $arr['driver']=$mail->mail_driver;
                $arr['host']=$mail->mail_host;
                $arr['port']=$mail->mail_port;
                $arr['encryption']=$mail->mail_encryption;
                $arr['username']=$mail->mail_username;
                $arr['password']=$mail->mail_password;
                $arr['from']['address']=$mail->mail_from_address;
                $arr['from']['name']=$mail->mail_from_name;
                $arr['markdown']['default']='markdown';
                $arr['markdown']['paths']=[resource_path('views/vendor/mail')];
                return $arr;
            }
            else{
                return [];
            }
        }
        catch (\Exception $e){
            // do nothing while creating tenant
            return [];
        }
        
    }

    // saml config mapping
    public function getSamlEntityIdAttribute(){
        try{
            $samlSettings = \DB::table('saml_settings')->first();
        }
        catch(\Exception $e){ 
            // do nothing while creaating tenant
            $samlSettings=null;
        }
        if($samlSettings){
            if($samlSettings->entity_id){
                \View::share('isSsoConfigured', true);
                \Config::set('saml2.ebdaa_idp_settings.idp.entityId', $samlSettings->entity_id);
                return $samlSettings->entity_id;
            }
            else{
                \View::share('isSsoConfigured', false);
            }
        }

        return null;
    }
    public function getSamlSsoAttribute(){
        try{
            $samlSettings = \DB::table('saml_settings')->first();
        }
        catch(\Exception $e){ 
            // do nothing while creaating tenant
            $samlSettings=null;
        }
        if($samlSettings){
            if($samlSettings->sso_url){
                \View::share('isSsoConfigured', true);
                return $samlSettings->sso_url;
            }
            else{
                \View::share('isSsoConfigured', false);
            }
        }

        return null;
    }
    public function getSamlSloAttribute(){
        try{
            $samlSettings = \DB::table('saml_settings')->first();
        }
        catch(\Exception $e){ 
            // do nothing while creaating tenant
            $samlSettings=null;
        }
        if($samlSettings){
            if($samlSettings->slo_url){
                \View::share('isSsoConfigured', true);
                return $samlSettings->slo_url;
            }
            else{
                \View::share('isSsoConfigured', false);
            }
        }

        return null;
    }
    public function getSamlCertMultiAttribute(){
        try{
            $samlSettings = \DB::table('saml_settings')->first();
        }
        catch(\Exception $e){ 
            // do nothing while creaating tenant
            $samlSettings=null;
        }
        if($samlSettings){
            if($samlSettings->is_x509certMulti){
                \View::share('isSsoConfigured', true);
                $x509certMulti = json_decode($samlSettings->certificate, true);
                return $x509certMulti;
            }
            else{
                \View::share('isSsoConfigured', false);
            }
        }

        return null;
    }
    public function getSamlCertAttribute(){
        try{
            $samlSettings = \DB::table('saml_settings')->first();
        }
        catch(\Exception $e){ 
            // do nothing while creaating tenant
            $samlSettings=null;
        }
        if($samlSettings){
            if($samlSettings->certificate){
                \View::share('isSsoConfigured', true);
                return $samlSettings->certificate;
            }
            else{
                \View::share('isSsoConfigured', false);
            }
            
        }

        return null;
    }
}
