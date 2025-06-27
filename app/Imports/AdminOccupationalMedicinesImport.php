<?php

namespace App\Imports;

use App\Http\Requests\Admin\OccupationalMedicineRequest;
use App\Services\Admin\OccupationalMedicineService;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class AdminOccupationalMedicinesImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    use SkipsFailures;

    private OccupationalMedicineService $service;

    public function __construct()
    {
        $this->service = app(OccupationalMedicineService::class);
    }

    public function model(array $row)
    {
        return $this->service->create($row);
    }

    public function rules(): array
    {
        return (new OccupationalMedicineRequest())->rules();
    }
}
