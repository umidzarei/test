<?php
namespace App\Models;

use App\Concerns\Models\Filterable;
use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory, Searchable, Filterable;

    protected $fillable = ['national_code', 'name', 'email', 'phone', 'photo'];

    public function organizationEmployee()
    {
        return $this->hasMany(OrganizationEmployee::class);
    }
    public function setPhoneAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['phone'] = null;
            return;
        }
        $this->attributes['phone'] = normalizePhone($value);
    }
    protected array $searchable = [
        'name',
        'national_code',
        'email',
        'phone',
    ];

}
