<?php

namespace App\Nova\Model;

use App\Exceptions\DomainCannotBeChangedException;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $tenant_id
 * @property string $domain
 * @property bool $is_primary
 * @property bool $is_fallback
 * @property string $certificate_status
 * @property Tenant $tenant
 */
class Domain extends BaseDomain
{
    protected $casts = [
        'is_primary' => 'bool',
        'is_fallback' => 'bool',
    ];

    public static function booted()
    {
        static::updating(function (self $model) {
            if ($model->getAttribute('domain') !== $model->getOriginal('domain')) {
                throw new DomainCannotBeChangedException;
            }
        });

        static::saved(function (self $model) {
            // There can only be one of these
            $uniqueKeys = ['is_primary', 'is_fallback'];

            foreach ($uniqueKeys as $key) {
                if ($model->$key) {
                    $model->tenant->domains()
                        ->where('id', '!=', $model->id)
                        ->update([$key => false]);
                }
            }
        });
    }

    public static function domainFromSubdomain(string $subdomain): string
    {
        return $subdomain . '.' . config('tenancy.central_domains')[0];
    }

    public function makePrimary(): self
    {
        $this->update([
            'is_primary' => true,
        ]);

        $this->tenant->setRelation('primary_domain', $this);

        return $this;
    }

    public function makeFallback(): self
    {
        $this->update([
            'is_fallback' => true,
        ]);

        $this->tenant->setRelation('fallback_domain', $this);

        return $this;
    }

    public function isSubdomain(): bool
    {
        return ! Str::contains($this->domain, '.');
    }

    /**
     * Get the domain type.
     * Returns 'subdomain' or 'domain'.
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return $this->isSubdomain() ? 'subdomain' : 'domain';
    }
}
