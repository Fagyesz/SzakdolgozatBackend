<?php

namespace App\Models;


namespace App\Models;

use App\Traits\ApiResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
/**
 * Class Tenant
 * @package App\Models
 * @property string name
 * @property string description
 * @property array extra
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, ApiResource, HasFactory;

    public static function getCustomColumns(): array
    {
        return ['id', 'name', 'description'];
    }

    public static function getDataColumn(): string
    {
        return 'extra';
    }
}
