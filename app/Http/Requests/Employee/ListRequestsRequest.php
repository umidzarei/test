<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRequestsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_id' => [
                'required',
                'integer',
                Rule::exists('organization_employees', 'organization_id')->where('employee_id', auth()->id()),
            ],
            'status' => 'nullable|string|in:pending,in_process,done,reject,pending_admin_approval',
        ];
    }
}
