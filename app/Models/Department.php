<?php
namespace App\Models;

use App\Concerns\Models\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['organization_id', 'name', 'description'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    protected array $searchable = [
        'name',
    ];
}
