<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Physician extends Model
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory;

    protected $fillable = ['name', 'email', 'password', 'phone', 'photo'];
    public function setPhoneAttribute($value)
    {
        if (is_null($value)) {
            $this->attributes['phone'] = null;
            return;
        }
        $this->attributes['phone'] = normalizePhone($value);
    }
}
