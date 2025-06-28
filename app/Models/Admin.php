<?php
namespace App\Models;

use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Model
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles, HasFactory, Searchable;

    protected $guard_name = 'admin';

    protected $fillable = ['name', 'email', 'phone', 'password'];
    protected $hidden = ['password'];

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
        'email',
        'phone',
    ];

    public function createdRequests()
    {
        return $this->morphMany(Request::class, 'requester');
    }
}
