<?php

namespace App\Imports;

use App\Http\Requests\Admin\PhysicianRequest;
use App\Services\Admin\PhysicianService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;


class AdminPhysiciansImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private PhysicianService $physicianService;

    public function __construct()
    {
        $this->physicianService = app(PhysicianService::class);
    }

    public function model(array $row)
    {
        return $this->physicianService->create($row);
    }

    public function rules(): array
    {
        return (new PhysicianRequest())->rules();
    }
}
