<?php
namespace App\Models;

use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class OrganizationAdmin extends Model
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory, Searchable;

    protected $fillable = ['organization_id', 'name', 'email', 'password', 'phone'];
    protected $hidden = [
        'password',
    ];
    protected $searchable = ['name', 'email', 'phone'];
    public function setPhoneAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['phone'] = null;
            return;
        }
        $this->attributes['phone'] = normalizePhone($value);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function createdRequests()
    {
        return $this->morphMany(Request::class, 'requester');
    }

}
