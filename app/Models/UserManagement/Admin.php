<?php

namespace App\Models\UserManagement;

use App\Notifications\AdminResetPasswordNotification;
use DarkGhostHunter\Laraguard\Contracts\TwoFactorAuthenticatable;
use DarkGhostHunter\Laraguard\TwoFactorAuthentication;
use Database\Factories\AdminFactory;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\UserManagement\AdminDepartment;
use \Mews\Purifier\Casts\CleanHtml;

class Admin extends Model implements Authenticatable, CanResetPasswordContract, TwoFactorAuthenticatable
{
    use AuthenticableTrait, HasFactory;
    use Notifiable;
    use CanResetPassword;
    use HasRoles;
    use TwoFactorAuthentication;

    protected $fillable = ['auth_method', 'first_name', 'last_name', 'email', 'password', 'contact_number_country_code', 'contact_number', 'is_sso_auth', 'status', 'last_login', 'require_mfa'];
    protected $appends = ['full_name', 'avatar'];
    protected $hidden = ['password', 'remember_token', 'session_id'];

    protected $casts = [
        'first_name'    => CleanHtml::class,
        'last_name' => CleanHtml::class,
        'email' => CleanHtml::class,
        'contact_number' => CleanHtml::class
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarAttribute()
    {
        return strtoupper(mb_substr($this->first_name, 0, 1)).''.strtoupper(mb_substr($this->last_name, 0, 1));
    }

    public function hasMfaRequired()
    {
        return $this->require_mfa;
    }

    public function department()
    {
        return $this->hasOne(AdminDepartment::class, 'admin_id');
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return AdminFactory::new();
    }
}
