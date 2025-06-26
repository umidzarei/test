<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'organization_id', 'job_position',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'organization_employee_departments',
            'organization_employee_id',
            'department_id'
        );
    }

}
