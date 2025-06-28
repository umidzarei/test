<?php
namespace App\Models;

use App\Concerns\Models\Filterable;
use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory, Searchable, Filterable;

    protected $fillable = [
        'name',
        'national_id',
        'reg_number',
        'economic_code',
        'logo',
        'address',
        'company_phone',
        'representative_name',
        'representative_position',
        'representative_phone',
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function organizationAdmins()
    {
        return $this->hasMany(OrganizationAdmin::class);
    }
    protected array $searchable = [
        'name',
        'national_id',
        'reg_number',
        'economic_code',
        'representative_name',
        'representative_position',
        'representative_phone',
    ];

    public function organizationEmployee()
    {
        return $this->hasMany(OrganizationEmployee::class);
    }

    public function healthRequests()
    {
        return $this->hasMany(Request::class, 'organization_id');
    }
}
